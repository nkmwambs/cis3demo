<fieldset>
  <legend>Codeigniter AWS S3 integration library: Browser Upload Demo</legend>
  <b>Description:</b> In this demo file(s) is being upload from browser to S3 bucket directly without uploading to server.<br/>
  <b>Traditional approach:</b> File firstly upload to server and then upload to S3 bucket, it takes double time in upload. 
  It is not efficient for big size files.<br/>
  <b>Browser upload:</b> In this approach the file directly upload to S3 bucket from browser, It takes less time in upload. 
  It is more efficient for big size files.<br/><br/>
  It is still 100% secure.   After upload you can check the uploaded file in below table. 
  <br/>
  
  If you require some manipulation before uploading file to S3 then check <?php echo anchor("cis3integration/manual_upload_demo","Demo2"); ?><br />
<br/>
<!-- Prepare File upload form-->
<form action="<?php echo $s3_details['url'];?>" class="direct-upload">
    <?php
    foreach ($s3_details['inputs'] as $name => $value) { ?>
        <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
    <?php } ?>
    <div class="control-group">
        <label for="file" class="control-label" title="Allowed file types: jpg|jpeg|png|gif|pdf|doc|docs|zip Max upload limit: 5MB">Choose a file to upload: <span style="color:red">*</span></label>
        <div class='controls'>
            <input type="hidden" name="key" value="">
            <input id="file" type="file" name="file" multiple onchange="return callFileValidation(this);" />
            <!-- Progress Bars to show upload completion percentage -->
            <div class="progress-bar-area"></div>
        </div>
    </div>
</form>  
<?php     
$attributes = array('class' => 'form-horizontal', 'id' => '');
echo form_open_multipart('', $attributes); 
?>    
  <div class="control-group">
    <label for="user_name" class="control-label">Your name:</label>
    <div class='controls'>
      <input id="user_name" type="text" name="user_name" maxlength="255" value="<?php echo set_value('user_name'); ?>"  />
      <?php echo form_error('user_name'); ?> </div>
  </div>
<div class="control-group" style="display: none;">
    <div class='controls'>
        <!-- This area will be filled with S3 upload results (mainly for debugging) -->
        <div>
            <h5>Files</h5>
            <textarea id="uploaded" name="uploaded_files"></textarea>
        </div>
    </div>
  </div>      
        
  <div class="control-group">
    <label></label>
    <div class='controls'> <?php echo form_submit( 'submit', 'Submit','class="btn"'); ?> </div>
  </div>
  <?php echo form_close(); ?>
</fieldset>
<?php
if($this->session->flashdata('msg')!="")
{
	?>
<div class="container" style="width:550px;float:left">
  <div class="alert alert-success"> <?php echo $this->session->flashdata('msg'); ?></div>
</div>
<?php
}
?>
<div>
  <table  class="table table-hover">
    <caption>
    <strong>Last 10 user uploaded files</strong>
    </caption>
    <?php
  $i=1;
	foreach($files_result->result() as $file)
	{
		?>
    <tr>
      <td><?php echo $i++; ?></td>
      <td><?php echo anchor(s3_site_url($file->s3_object_key),"Download","Title = 'File S3 Bucket URL: ".s3_site_url($file->s3_object_key)."'"); ?></td>
      <td><?php echo anchor(site_url("cis3integration/delete_file/".$file->id),"Delete","Title = 'Click here to delete the file from S3 Bucket' onClick='return confirm(\"Are you sure you want to delete this file?\")'"); ?></td>
      <td><?php  echo "Uploaded by: ".$file->user_name;?></td>
    </tr>
    <?php	
	}
	if($i==1)
	{?>
    <tr>
      <td colspan="2"> You didn't uploaded any files yet</td>
    </tr>
    <?php
	}
	?>
  </table>
</div>


<!-- Start of the JavaScript -->
<!-- Load jQuery & jQuery UI (Needed for the FileUpload Plugin) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

<!-- Load the FileUpload Plugin (more info @ https://github.com/blueimp/jQuery-File-Upload) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.5.7/jquery.fileupload.js"></script>
<!--Load Javascript library-->
<script src="<?php echo site_url("js/cis3integration_lib.js");?>"></script>      

<script>
    var config = {
        // Place any uploads within the descending folders
        // so ['test1', 'test2'] would become /test1/test2/filename
        //var folders = ['uploads'];        
        upload_path:['uploads'],
        allowed_types: ['jpeg', 'jpg', 'png', 'gif', 'bmp', 'pdf', 'doc', 'docs', 'zip'],
        max_size:5120,
        make_unique_filename:true,
        s3_key_field:"key",
        progress_loader_div:"progress-bar-area",
        uploaded_files_field_id:"uploaded"
    };
    ciS3Integration.setConfig(config);
    ciS3Integration.s3Upload("direct-upload");
    function callFileValidation(fileField){
      // Then we can call our custom function using
        //return false;      
        return ciS3Integration.doFileValidation(fileField);                  
    }
</script>
