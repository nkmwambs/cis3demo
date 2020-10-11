<fieldset>
  <legend>Codeigniter AWS S3 integration library Demo2, </legend>
  Description: In this demo a photo is being upload using CodeIgniter upload library's do_upload method and then utilize CodeIgniter's Image Manipulation Class to resize the photo to 200*250 pixels and finally using "Codeigniter AWS S3 integration library" it uploads to an S3 bucket. It is a good example how you can utilize our library in your project in case you need some sort of manipulation on user uploaded file before actually saving in S3 bucket.
  If you require direct upload to S3 without manipulatin then check <?php echo anchor("cis3integration","Demo1"); ?><br />
  <?php     
$attributes = array('class' => 'form-horizontal', 'id' => '');
echo form_open_multipart('', $attributes); ?>
  <div class="control-group">
    <label for="file" class="control-label" title="Allowed file types: jpg|jpeg|png|gif Max upload limit: 5MB">Choose a file to upload: <span style="color:red">*</span></label>
    <div class='controls'>
      <input id="file" type="file" name="file" />
      <?php echo form_error('file'); ?> </div>
  </div>
  <div class="control-group">
    <label for="user_name" class="control-label">Your name:</label>
    <div class='controls'>
      <input id="user_name" type="text" name="user_name" maxlength="255" value="<?php echo set_value('user_name'); ?>"  />
      <?php echo form_error('user_name'); ?> </div>
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
      <td><?php echo anchor(site_url("cis3integration/delete_file/".$file->id),"Delete","Title = 'Click here to delete the file from S3 Bucket'  onClick='return confirm(\"Are you sure you want to delete this file?\")'"); ?></td>
      <td><?php  echo "Uploaded by: ".$file->user_name;?></td>
    </tr>
    <?php	
	}
	if($i==1)
	{?>
    <tr>
      <td colspan="2"> You didn't uploaded any photos yet</td>
    </tr>
    <?php
	}
	?>
  </table>
</div>

