<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter AWS S3 Integration Library
 * Demo Controller of CodeIgniter AWS S3 Integration Library
 *
 * It includes 2 demo one is simple upload to S3 bucket and one includes file manipulation on server then copy to S3 Bucket
 *
 * @package    CodeIgniter AWS S3 Integration Library
 * @author     scriptigniter <scriptigniter@gmail.com>
 * @link       http://www.scriptigniter.com/cis3demo/
 */

class Cis3integration extends CI_Controller
{
    private $custom_errors = array();
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
        
        $this->load->helper(array(
            'form',
            'url',
            'cis3integration_helper',
            'file'
        ));
        
        //optinally pass config array as per need.
        $config = array(
            'bucket_name' => 'compassion-fcp-fms-version2',
            'region' => 'eu-west-1',
            'scheme' => 'https'
        );
	$this->load->library('cis3integration_lib',$config);
        
        $this->load->model('cis3integration_model');
    }

	/**
     * Demo 1, Simple file upload to S3 bucket
     *
     * @access public
     */    
    public function index()
    {
        $this->file_name = "";
        $this->upload_data = "";
        if (@$_FILES['file']['name'] != "") 
        {
            $config['upload_path']   = 'uploads/';//Leave blank if want to upload at root of bucket
            $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docs|zip';
            $config['remove_spaces'] = TRUE;
            $config['max_size']      = '5120';//5MB
            
            //S3 integration library config
            $config['acl'] = 'public-read';
            $config['make_unique_filename'] = true;
            
            $this->_upload_file($config, 'file');
            $this->form_validation->set_rules('file', 'file', 'callback__check_file[file]');
        }
        else
        {
            $this->form_validation->set_rules('file', 'File', 'required');
        }
        
        $this->form_validation->set_rules('user_name', 'user_name', 'max_length[255]|xss_clean');        
        $this->form_validation->set_error_delimiters('<br /><span style="color:red">', '</span>');      
        
        $data['files_result'] = $this->db->query("SELECT * from files WHERE type LIKE 'demo1' ORDER by id DESC LIMIT 10");
        $data['page_title'] = "Demo 1";
	if ($this->form_validation->run() == FALSE) // validation hasn't been passed
        {
            $this->load->view('header',$data);
            $this->load->view('upload', $data);
            $this->load->view('footer');
        }
        else//validation passed
        {
            $user_name = @$this->input->post('user_name') != "" ? @$this->input->post('user_name') : "Anonymous";
            $form_data = array(
                's3_object_key' => $this->upload_data['full_path'],
                'user_name' => @$user_name,
                'type' => 'demo1'
            );
            // run insert model to write data to db
            if ($this->cis3integration_model->SaveForm($form_data) == TRUE) // the information has therefore been successfully saved in the db
            {
                $this->session->set_flashdata('msg', 'File has been successfully uploaded, Check below table to ensure.');
                redirect('cis3integration'); // or whatever logic you need
            }
            else
            {
                $this->session->set_flashdata('msg', 'An error occurred saving your information. Please try again later.');
                redirect('cis3integration'); // or whatever logic you need
            }
        }
    }
    
    /**
     * Demo 2, Image upload to S3 bucket after resizing
     *
     * @access public
     */    
    public function manual_upload_demo()
    {
        $this->file_name = "";
        $this->upload_data = "";
        if (@$_FILES['file']['name'] != "")
	{
            $config['upload_path']   = 'user_photos/';//Leave blank if want to upload at root of bucket
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['remove_spaces'] = TRUE;
            $config['max_size']      = '5120';//5MB
            
            //S3 integration only config
            $config['acl'] = 'public-read';
            $config['make_unique_filename'] = false;
            
            $this->_upload_photo($config, 'file');
            $this->form_validation->set_rules('file', 'file', 'callback__check_file[file]');
        }
        else
        {
            $this->form_validation->set_rules('file', 'File', 'required');
        }
        $this->form_validation->set_rules('user_name', 'user_name', 'max_length[255]|xss_clean');
        $this->form_validation->set_error_delimiters('<br /><span style="color:red">', '</span>');

        $data['files_result'] = $this->db->query("SELECT * from files WHERE type LIKE 'demo2' ORDER by id DESC LIMIT 10");
        $data['page_title'] = "Demo 2";
        if ($this->form_validation->run() == FALSE) // validation hasn't been passed
        {
            $this->load->view('header',$data);
            $this->load->view('upload_photo', $data);
            $this->load->view('footer');
        }
	else // passed validation proceed to post success logic
        {
            $user_name = @$this->input->post('user_name') != "" ? @$this->input->post('user_name') : "Anonymous";
            $form_data = array(
                's3_object_key' => $this->upload_data['full_path'],
                'user_name' => @$user_name,
                'type' => 'demo2'
            );
            
            // run insert model to write data to db
            if ($this->cis3integration_model->SaveForm($form_data) == TRUE) // the information has therefore been successfully saved in the db
            {
                $this->session->set_flashdata('msg', 'File has been successfully uploaded, Checkout below table to ensure.');
                redirect('cis3integration/manual_upload_demo'); // or whatever logic you need
            }
            else
            {
                $this->session->set_flashdata('msg', 'An error occurred saving your information. Please try again later.');
                redirect('cis3integration'); // or whatever logic you need
            }
        }
    }
	
    /**
     * Delete File from S3 Bucket
     *
     * @access public
     */    
    public function delete_file($file_id="")
    {
            if(!$file_id)
            {
                    $this->session->set_flashdata('msg', 'Nothing to delete.');
                    redirect('cis3integration');
            }

            if($this->cis3integration_model->delete_file($file_id)==true)
            {
                    $this->session->set_flashdata('msg', 'File successfully deleted from S3 Bucket.');
                    $this->load->library('user_agent');
                    redirect($this->agent->referrer());// or whatever logic you need
            }
            else
            {
                    $this->session->set_flashdata('msg', 'Unable to delete file.');
                    redirect('cis3integration'); // or whatever logic you need
            }

    }
    
    /**
     * Check file upload error occured or not, If occured then set the form field error.
     *
     * @access private
     */    
    function _check_file($field, $field_value)
    {
        if (isset($this->custom_errors[$field_value]))
		{
            $this->form_validation->set_message('_check_file', $this->custom_errors[$field_value]);
            unset($this->custom_errors[$field_value]);
            return FALSE;
        }
        return TRUE;
    }
	
	/**
     * File upload to S3 Bucket
     *
     * @access private
     */    
    function _upload_file($config, $field_name)
    {
        $this->load->library('upload');
        $this->upload->initialize($config);
        if (!$this->upload->do_upload_s3($field_name))
        {
            $this->custom_errors[$field_name] = $this->upload->display_errors();
        }
        else
        {
            $this->upload_data = $this->upload->data();
        }        
    }
	
	/**
     * Upload Image to server, Resize that and then upload to S3 Bucket
     *
     * @access private
     */    
    
    function _upload_photo($config, $field_name)
    {
        $this->load->library('upload');
        $this->upload->initialize($config);
        if (!$this->upload->do_upload($field_name))
	{
            $this->custom_errors[$field_name] = $this->upload->display_errors();
        }
        else
        {
            $upload_data = $this->upload->data();
            $uploaded_name = $upload_data['file_name'];
            $row_name = $upload_data['raw_name'];
            $ext = $upload_data['file_ext'];

            $config                   = array();
            $unique_name              = '_thumb' . rand(1, 9999);
            $config['image_library']  = 'gd2';
            $config['source_image']   = 'user_photos/' . $uploaded_name;
            $config['maintain_ratio'] = FALSE;
            $config['overwrite']      = TRUE;
            $config['width']          = 200;
            $config['height']         = 250;
            $config['new_image']      = $new_image = 'user_photos/' . $row_name . $unique_name . $ext;
            $this->load->library('image_lib', $config);
            $this->image_lib->initialize($config);
            if (!$this->image_lib->resize()) //if file does not resize then use full size image
            {
                    //Not resized so use existing file
                    $photo_file_name = $uploaded_name;
            }
            else
            {
                    //resized so use resized file
                    $photo_file_name = $row_name . $unique_name . $ext;
                    @unlink('user_photos/' . $uploaded_name);
            }
            //Now upload the resized file to s3 bukcet
            if (!$this->upload->do_upload_manually("user_photos/", $photo_file_name))
            //Optionally you can pass the S3 path as third parameter if you need to upload file at different location in S3 Bucket like $this->upload->do_upload_manually("user_photos/", "myfile.jpg","myphotos/") 
            //In short: $this->upload->do_upload_manually(from location, file name, copy to this location in bukcet)
            {
                $photo_file_name = "";
                //Most importasnt part reset image library
                $this->image_lib->clear();
                return false;
            }
            else
            {
                $data = array(
                        'upload_data' => $this->upload->data()
                );
                $photo_file_name = $data['upload_data']['file_name'];
                $this->file_name = $data['upload_data']['file_name'];
                $this->upload_data = $this->upload->data();

                //Most importasnt part reset image library
                $this->image_lib->clear();            
                return true;
            }
        }   
    }	
	
	
    /**
     * Demo 3, Copy a file in S3 bucket,
     * You can also copy a file from one bucket to another bucket
	 *
     * @access public
     * To run this demo Ensure that you already have myfile.jpg in your bucket, If you do not have this file then upload using S3 Console or using our upload demo.
    */
    function copy_s3_file()
    {		        
        $flag = $this->cis3integration_lib->copy_s3_file("myfile.jpg","copy_of_myfile.jpg");	
        if($flag)
        {
            $data = array("message"=>"File 'myfile.jpg' successfully copied as 'copy_of_myfile.jpg' in '".BUCKET_NAME."' Bucket<br>
            See here ".anchor(s3_site_url('copy_of_myfile.jpg'),"File"));
            $data['page_title'] = "Copy S3 File Demo";
            $this->load->view("message",$data);
        }
        else
        {
            $data = array("message"=>"There is some error to copy the file 'myfile.jpg' as 'copy_of_myfile.jpg', Please try again after some time");
            $this->load->view("message",$data);

        }
    }
	
    /**
     * Demo 4, To create a bucket in your AWS accout
     *
     * @access public
     */    	
    function create_bucket($bucket_name="")
    {
        $data['page_title'] = "Create Bucket Demo";
        $bucket_name = "cis3demotestingbucket";//Change it with URI one to make it dynamic
        $flag = $this->cis3integration_lib->create_bucket($bucket_name);	
        if($flag)
        {
                $data['message'] = "Bucket '$bucket_name' successfully created.<br/>
                To prevent the abusing of system only creation of '$bucket_name' is allowed in this demo and that bucket is already exist in my AWS account, Once you buy this script you can easily create as many buckets you want in your AWS account.";                
                $this->load->view("message",$data);
        }
        else
        {
                $data['message'] = "To prevent the abusing of system only creation of '$bucket_name' is allowed in this demo and that bucket is already exist in my AWS account, Once you buy this script you can easily create as many buckets you want in your AWS account.";
                $this->load->view("message",$data);
        }
    }
    
    /**
     * Demo 5, To create a Presigned URL
     *
     * @access public
     */    	
    function create_presigned_url()
    {        
        $presignedUrl = $this->cis3integration_lib->get_presigned_url("myfile.jpg",10);	
        if($presignedUrl)
        {
                $data = array("message"=>"Pre-signed URL successfully generated.<br/><a href='$presignedUrl'>Click here</a> to see the URL.
                    <br/>
                    This URL will be valid for next 10 seconds after that you will not be able to access this.
                    <br/><br/>
                Note: To prevent the abusing of system only pre-signed URL of myfile.jpg is allowed 
                in this demo and that object is already exist in bucket, 
                Once you buy this script you can easily create as many pre-signed url as you want. 
                If you want to know about S3 Pre-signed URL, Refer below URL<br/>"
                        . "<a href='http://docs.aws.amazon.com/AmazonS3/latest/dev/ShareObjectPreSignedURL.html'>http://docs.aws.amazon.com/AmazonS3/latest/dev/ShareObjectPreSignedURL.html</a>"
                    . "<br/>"
                        . "");
                $data['page_title'] = "Create Presigned URL Demo";
                $this->load->view("message",$data);
        }
        else
        {
                $data = array("message"=>"To prevent the abusing of system only presigned URL of myfile.jpg is allowed in this demo and that object is already exist in bucket "+$bucket_name+", Once you buy this script you can easily create as many presignedurl as you want.");
                $this->load->view("message",$data);
        }
    }   
    
    /**
     * Demo 6, Browser upload
     *
     * @access public
     */    	
    function browser_upload()
    {
        $data['page_title'] = "S3 Browser Upload Demo";
        $this->form_validation->set_rules('user_name', 'user_name', 'max_length[255]|xss_clean');        
        $this->form_validation->set_rules('uploaded_files', '', 'required');
        $this->form_validation->set_error_delimiters('<br /><span style="color:red">', '</span>');      
        
        $data['files_result'] = $this->db->query("SELECT * from files WHERE type LIKE 'demo_browser_upload' ORDER by id DESC LIMIT 10");
        
	if ($this->form_validation->run() == FALSE) // validation hasn't been passed
        {            
            //S3 integration library config
            $config['acl'] = 'public-read';
            $data['s3_details'] = $this->cis3integration_lib->getS3Details($config['acl']);	
            
            $this->load->view('header',$data);
            $this->load->view('browser_upload', $data);
            $this->load->view('footer');
        }
        else//validation passed
        {
            $user_name = @$this->input->post('user_name') != "" ? @$this->input->post('user_name') : "Anonymous";
            $uploaded_files = @$this->input->post('uploaded_files') != "" ? @$this->input->post('uploaded_files') : "";
            $uploaded_files = json_decode($uploaded_files);
            foreach ($uploaded_files as $file){
                echo $original_name = $file->original_name;
                echo $s3_name = $file->s3_name;
                echo $size = $file->size;
                echo $url = $file->url;
                //exit;
                $form_data = array(
                    's3_object_key' => $s3_name,
                    'user_name' => @$user_name,
                    'type' => 'demo_browser_upload'
                );
                // run insert model to write data to db
                $this->cis3integration_model->SaveForm($form_data); // the information has therefore been successfully saved in the db
            }
            $this->session->set_flashdata('msg', 'File has been successfully uploaded, Check below table to ensure.');
            redirect('cis3integration/browser_upload'); // or whatever logic you need            
        }
       
    }  
}