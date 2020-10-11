<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter AWS S3 Integration Library
 * 
 * @package    CodeIgniter AWS S3 Integration Library
 * @author     scriptigniter <scriptigniter@gmail.com>
 * @link       http://www.scriptigniter.com/cis3demo/
 */



class Cis3integration_lib 
{
    //Refer "Amazon Simple Storage Service (Amazon S3)" section 
    //on http://docs.aws.amazon.com/general/latest/gr/rande.html 
    //for supported regions/scheme(protocol)/version.
    public $bucket_name = "compassion-fcp-fms-version2";
    public $region = 'eu-west-1';
    public $version = 'latest';
    public $scheme = 'https';
    public $access_key = '';
    public $secret_key = '';
    public $s3_url = '';    
    
    public $s3Client = null;
    
    public function __construct(array $config = array(), $reset = TRUE)
    {
        $reflection = new ReflectionClass($this);
        if ($reset === TRUE)
        {
                $defaults = $reflection->getDefaultProperties();
                foreach (array_keys($defaults) as $key)
                {
                        if ($key[0] === '_')
                        {
                                continue;
                        }

                        if (isset($config[$key]))
                        {
                                if ($reflection->hasMethod('set_'.$key))
                                {
                                        $this->{'set_'.$key}($config[$key]);
                                }
                                else
                                {
                                        $this->$key = $config[$key];
                                }
                        }
                        else
                        {
                                $this->$key = $defaults[$key];
                        }
                }
        }
        else
        {
                foreach ($config as $key => &$value)
                {
                        if ($key[0] !== '_' && $reflection->hasProperty($key))
                        {
                                if ($reflection->hasMethod('set_'.$key))
                                {
                                        $this->{'set_'.$key}($value);
                                }
                                else
                                {
                                        $this->$key = $value;
                                }
                        }
                }
        }

        //AWS account setting
        define('AWS_ACCESS_KEY',"AKIAXRNTNANX2KPACJWY");
        $this->access_key = AWS_ACCESS_KEY;
        define('AWS_SECRET_KEY',"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");
        $this->secret_key = AWS_SECRET_KEY;

        define('BUCKET_NAME',$this->bucket_name);//The bucket name you want to use for your project
        define('AWS_URL','http://'.$this->bucket_name.'.s3.amazonaws.com/');
        $this->s3_url = AWS_URL;

        /*define('AWS_ACCESS_KEY',"{{AWS_ACCESS_KEY}}");
        $this->access_key = AWS_ACCESS_KEY;
        define('AWS_SECRET_KEY',"{{AWS_SECRET_KEY}}");
        $this->access_key = AWS_SECRET_KEY;

        define('BUCKET_NAME','{{BUCKET_NAME}}');//The bucket name you want to use for your project
        define('AWS_URL','https://'.BUCKET_NAME.'.s3.amazonaws.com/');
        $this->s3_url = AWS_URL;
        */

        //check AWS access key is set or not
        if(trim(AWS_ACCESS_KEY,"{}")=="AWS_ACCESS_KEY")
        {
            exit("CI S3 Integration configuration error! Please input the AWS Access Key, "
                        . "AWS Secret Key and Bucket Name in applicatin/libraries/cis3integration_lib.php file");
        }
        //require_once('awssdk3/aws-autoloader.php');	
        require_once (APPPATH."../vendor/autoload.php");

        //Create S3 client
        $sharedConfig = [
            'region'  => $this->region,
            'version' => $this->version,
            'scheme' => $this->scheme,
            'credentials' => [
                'key'    => AWS_ACCESS_KEY,
                'secret' => AWS_SECRET_KEY,
            ],
        ];
        $sdk = new Aws\Sdk($sharedConfig);
        $this->s3Client = $sdk->createS3();                
    }

    /**
     * Delete S3 Object
     *
     * @access public
     */    	
    function delete_s3_object($file_path)
    {
            $response = $this->s3Client->deleteObject(array(
                'Bucket'     => $this->bucket_name,
                'Key'        => $file_path
            ));
            return true;
    }

    /**
     * Copy S3 Object
     *
     * @access public
     */ 
    function copy_s3_file($source,$destination)
    {
            $response = $this->s3Client->copyObject(array(
                'Bucket'     => $this->bucket_name,
                'Key'        => $destination,
                'CopySource' => "{$this->bucket_name}/{$source}",
            ));
            if($response['ObjectURL'])
            {
                return true;
            }
            return false;
    }

    /**
     * Create a new bucket in already specified region
     *
     * @access public
     */ 
    function create_bucket($bucket_name="",$region="")
    {
            $promise = $this->s3Client->createBucketAsync(['Bucket' => $bucket_name]);
            try {
                $result = $promise->wait();
                return true;
            } catch (Exception $e) {
                //echo "exception";exit;
                //echo $e->getMessage();
               return false;
            }		
    }
    
    /**
     * Create a presigned URL.     
     * @access public
     * Object key: Objecy key of S3 file.
     * Duration: duration of presigned URL in seconds, After that URL will not be accessible.
     */ 
    function get_presigned_url($object_key="",$duration="10")
    {
            $cmd = $this->s3Client->getCommand('GetObject', [
                'Bucket' => $this->bucket_name,
                'Key'    => $object_key
            ]);

            //Create presigned request
            $request = $this->s3Client->createPresignedRequest($cmd, "+$duration seconds");

            // Get the presigned url
            $presignedUrl = (string) $request->getUri();
            return $presignedUrl;
    }
    
    function getS3Details($acl = "private") {
        $algorithm = "AWS4-HMAC-SHA256";
        $service = "s3";
        $date = gmdate("Ymd\THis\Z");
        $shortDate = gmdate("Ymd");
        $requestType = "aws4_request";
        $expires = "86400"; // 24 Hours
        $successStatus = "201";
        $url = "//{$this->bucket_name}.s3.amazonaws.com";

        // Step 1: Generate the Scope
        $scope = [
            $this->access_key,
            $shortDate,
            $this->region,
            $service,
            $requestType
        ];
        $credentials = implode('/', $scope);

        // Step 2: Making a Base64 Policy
        $policy = [
            'expiration' => gmdate('Y-m-d\TG:i:s\Z', strtotime('+6 hours')),
            'conditions' => [
                ['bucket' => $this->bucket_name],
                ['acl' => $acl],
                ['starts-with', '$key', ''],
                ['starts-with', '$Content-Type', ''],
                ['success_action_status' => $successStatus],
                ['x-amz-credential' => $credentials],
                ['x-amz-algorithm' => $algorithm],
                ['x-amz-date' => $date],
                ['x-amz-expires' => $expires],
            ]
        ];
        $base64Policy = base64_encode(json_encode($policy));

        // Step 3: Signing your Request (Making a Signature)
        $dateKey = hash_hmac('sha256', $shortDate, 'AWS4' . $this->secret_key, true);
        $dateRegionKey = hash_hmac('sha256', $this->region, $dateKey, true);
        $dateRegionServiceKey = hash_hmac('sha256', $service, $dateRegionKey, true);
        $signingKey = hash_hmac('sha256', $requestType, $dateRegionServiceKey, true);

        $signature = hash_hmac('sha256', $base64Policy, $signingKey);

        // Step 4: Build form inputs
        // This is the data that will get sent with the form to S3
        $inputs = [
            'Content-Type' => '',
            'acl' => $acl,
            'success_action_status' => $successStatus,
            'policy' => $base64Policy,
            'X-amz-credential' => $credentials,
            'X-amz-algorithm' => $algorithm,
            'X-amz-date' => $date,
            'X-amz-expires' => $expires,
            'X-amz-signature' => $signature
        ];

        return compact('url', 'inputs');
    }
	
}