<fieldset>
  <legend>Codeigniter AWS S3 integration library Demo1</legend>
  Description: In this demo a file is being upload to an S3 bucket using "Codeigniter AWS S3 integration library". After upload you can check the uploaded file in below table. 
  If you require some manipulation before uploading file to S3 then check <?php echo anchor("cis3integration/manual_upload_demo","Demo2"); ?><br />
  <?php     
$attributes = array('class' => 'form-horizontal', 'id' => '');
echo form_open_multipart(base_url().'Cis3integration', $attributes); ?>
  <div class="control-group">
    <label for="file" class="control-label" title="Allowed file types: jpg|jpeg|png|gif|pdf|doc|docs|zip Max upload limit: 5MB">Choose a file to upload: <span style="color:red">*</span></label>
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
