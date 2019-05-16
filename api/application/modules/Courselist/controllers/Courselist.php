<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json');

class Courselist extends CI_Controller 
{	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Courselist_model');
	}

	public function getAllCourse()
	{
		$data['course']=$this->Courselist_model->getlist_Course();
		mysqli_next_result($this->db->conn_id);
		$data['sub']=$this->Courselist_model->getlist_SubCategory();

		$data['Inst']=$this->Courselist_model->getlist_Instructors();
		//$data['skill']=$this->Course_model->get_skilldata($Course_id);
		//print_r($data);
		echo json_encode($data);
	}
	public function getCategoryWise($Category_Id = NULL) {
		
		if(!empty($Category_Id)) {					
			
			$result = [];
			$result = $this->Courselist_model->getCategoryWiseList($Category_Id);	
			//print_r($result);		
			echo json_encode($result);				
		}			
	}
	public function getInstWise($User_Id = NULL) {
		
		if(!empty($User_Id)) {					
			
			$result = [];
			$result = $this->Courselist_model->getInstWiseList($User_Id);	
			//print_r($result);		
			echo json_encode($result);				
		}			
	}
	public function getAllCourseDetail() {
		
		$post_getAllCourseDetail = json_decode(trim(file_get_contents('php://input')), true);
		if($post_getAllCourseDetail) 
		{	
			$data = [];
			$data['detail'] = $this->Courselist_model->getCourseDetailList($post_getAllCourseDetail);
			$data['session'] = $this->Courselist_model->getCourseSessionList($post_getAllCourseDetail);
			$data['Preview'] = $this->Courselist_model->PreviewgetCourseSessionList($post_getAllCourseDetail);
			$data['CourseContent']=$this->Courselist_model->get_CourseContent($post_getAllCourseDetail);
			echo json_encode($data);				
		}			
	}
	public function getDiscussionById() {
		$post_data = json_decode(trim(file_get_contents('php://input')), true);	
		if(!empty($post_data['CourseId'])) {					
			$data = [];
			$data['discussion'] = $this->Courselist_model->getDiscussionById($post_data['CourseId']);
			$data['skill']=$this->Courselist_model->get_skilldata($post_data['CourseId']);	
			$data['Reviews']=$this->Courselist_model->getReviews($post_data);					
			//print_r($data['CourseContent']);
			//print_r($data);
			echo json_encode($data);				
		}			
	}
	public function getAllDiscussions($Id = NULL) {
		
		if(!empty($Id)) {					
			$data = [];
			$data['CourseName'] = $this->Courselist_model->getCourseName($Id);
			$data['discussion'] = $this->Courselist_model->getAllDiscussions($Id);
			echo json_encode($data);				
		}			
	}
	public function getAllReviews() {
		$post_data = json_decode(trim(file_get_contents('php://input')), true);
		if(!empty($post_data['CourseId'])) {					
			$data = [];
			$data['CourseName'] = $this->Courselist_model->getCourseName($post_data['CourseId']);
			$data['Reviews']=$this->Courselist_model->getAllReviews($post_data);		
			echo json_encode($data);				
		}			
	}
	public function getUserDetail($Id = NULL) {
		
		if(!empty($Id)) {					
			$data = [];
			$data = $this->Courselist_model->getUserDetail($Id);	
			echo json_encode($data);				
		}			
	}
	public function addEnroll()
	{
		
		$post_addEnroll = json_decode(trim(file_get_contents('php://input')), true);
		if ($post_addEnroll) 
			{ 
					$result = $this->Courselist_model->addEnroll($post_addEnroll);
					if($result){

						$this->db->select('Value');
						$this->db->where('Key','EmailFrom');
						$smtp1 = $this->db->get('tblmstconfiguration');	
						foreach($smtp1->result() as $row) {
							$smtpEmail = $row->Value;
						}
						$this->db->select('Value');
						$this->db->where('Key','EmailPassword');
						$smtp2 = $this->db->get('tblmstconfiguration');	
						foreach($smtp2->result() as $row) {
							$smtpPassword = $row->Value;
						}
						
						$config['protocol']='smtp';
						$config['smtp_host']='ssl://smtp.googlemail.com';
						$config['smtp_port']='465';
						$config['smtp_user']='myopeneyes3937@gmail.com';
						$config['smtp_pass']='W3lc0m3@2019';	
		
						// $config['protocol']='mail';
						// $config['smtp_host']='vps40446.inmotionhosting.com';
						// $config['smtp_port']='587';
						// $config['smtp_user']=$smtpEmail;
						// $config['smtp_pass']=$smtpPassword;
						
						$config['charset']='utf-8';
						$config['newline']="\r\n";
						$config['mailtype'] = 'html';							
						$this->email->initialize($config);
						$Subject = 'LMS - Payment successfully';
						$body = '<table border="0" cellpadding="0" cellspacing="0" style="border:1px solid #333333; color:#000000; font-family:Arial,Helvetica,sans-serif; font-size:15px; line-height:22px; margin:0 auto; width:600px">
						<tbody>
							<tr>
								<td style="background-color:#f3f3f3; background:#f3f3f3; border-bottom:1px solid #333333; padding:10px 10px 5px 10px"><img alt="Learn Feedback" src="'.BASE_URL.'/assets/images/logo.png" /></td>
							</tr>
							<tr>
								<td style="border-width:0; padding:20px 10px 10px 10px; text-align:center">
								<p style="color:#000; font-size: 25px; line-height: 25px; font-weight: bold;padding: 0; margin: 0 0 10px;"><strong>Payment Succesfully </strong><strong></strong></p>
					
								<p style="color:#000; font-size: 18px; line-height: 18px; font-weight: bold; padding: 0; margin: 0 0 10px;">We&rsquo;re so happy you&rsquo;ve joined us.</p>
					
								<p style="color:#000; font-size: 14px; line-height:20px; padding: 0; margin: 0 0;">Use the button below to login your account and get started:</p>
								</td>
							</tr>
								<tr>
								<td style="border-width:0; padding:0; text-align:center; vertical-align:middle">
								<table border="0" cellpadding="0" cellspacing="0" style="border:0; font-family:Arial,Helvetica,sans-serif; font-size:15px; line-height:22px; margin:0 auto">
									<tbody>
										<tr>
											<td style="background-color:#b11016; background:#b11016; border-radius:4px; border-width:0; clear:both; color:#ffffff; font-size:14px; line-height:13px; opacity:1; padding:10px; text-align:center; text-decoration:none; width:130px"><a href="{login_link}" style="color:#fff; text-decoration:none;">Get Started</a></td>
										</tr>
									</tbody>
								</table>
								</td>
							</tr>
								<tr>
								<td style="border-width:0; padding:20px 10px 10px 10px; text-align:center; vertical-align:middle">			
								<p style="color:#777; font-size: 14px; line-height:20px; padding: 0; margin: 0 0 25px;">If you have any questions, you can reply to this email and it will go right to them. Alternatively, feel free to contact our customer success team anytime.</p>
					
								<p style="color:#777; font-size: 12px; line-height:20px; padding: 0; margin: 0 0 10px; text-align: left;">If you&rsquo;re having trouble with the button above, copy and paste the URL below into your web browser. <a href="{login_link}" style="cursor:pointer;">click here</a></p>
								</td>
							</tr>
							<tr>
								<td style="background-color:#222222; background:#222222; border-top:1px solid #cccccc; color:#ffffff; font-size:13px; padding:7px; text-align:center">Copyright &copy; 2018 Learning Management System</td>
							</tr>
						</tbody>
					</table>';
		
					$body = str_replace("{login_link}",''.BASE_URL.'/login',$body);
		
						$this->email->from($smtpEmail, 'LMS');
						$this->email->to('nirav.patel@theopeneyes.in');		
						$this->email->subject($Subject);
						$this->email->message($body);
						if($this->email->send())
						{
							echo json_encode('success');
						}	
					}
			}
	}
	
	public function delete() {
		$post_Cart = json_decode(trim(file_get_contents('php://input')), true);		

		if ($post_Cart)
		 {
			if($post_Cart['id'] > 0){
				$result = $this->Courselist_model->delete_Cart($post_Cart);
				if($result) {
					
					echo json_encode("Delete successfully");
					}
		 	}
		
			
		} 
			
	}
	public function checkUser() {
		$post_data = json_decode(trim(file_get_contents('php://input')), true);		

		if ($post_data)
		 {
				$result = $this->Courselist_model->checkUser($post_data);
				if($result) {
					echo json_encode("Duplicate User");
				}
		} 
			
	}
	public function addPost()
		{
			$post_data = json_decode(trim(file_get_contents('php://input')), true);	
			if ($post_data) {
				if(isset($post_data['DiscussionId']) && $post_data['DiscussionId']>0)
				{
					$result = $this->Courselist_model->editPost($post_data);
				}
				else{
					$result = $this->Courselist_model->addPost($post_data);
				}
				if($result) {
					echo json_encode($result);	
				}	
			}	
		}
	public function addCommentReply()
	{
		$post_data = json_decode(trim(file_get_contents('php://input')), true);	
		if ($post_data) {
				if(isset($post_data['DiscussionId']) && $post_data['DiscussionId']>0)
				{
					$result = $this->Courselist_model->editCommentReply($post_data);
				}
				else{
				$result = $this->Courselist_model->addCommentReply($post_data);
				}
				if($result) {
					echo json_encode($result);	
				}	
		}	
	}
	public function addReview()
	{
		$review_data = json_decode(trim(file_get_contents('php://input')), true);	
		if ($review_data) {
			if(isset($review_data['ReviewId']) && $review_data['ReviewId']>0)
			{
				$result['ReviewData'] = $this->Courselist_model->editReview($review_data);
				$result['Reviews']=$this->Courselist_model->getReviews($review_data);
			}
			else{
				$result['ReviewData'] = $this->Courselist_model->addReview($review_data);
				$result['Reviews']=$this->Courselist_model->getReviews($review_data);
			}
			if($result) {
				echo json_encode($result);	
			}
		}	
	}
	public function deleteReview() {
		$post_data = json_decode(trim(file_get_contents('php://input')), true);		
		if(!empty($post_data['ReviewId'])) {					
			$data['ReviewData'] = $this->Courselist_model->deleteReview($post_data['ReviewId']);
			$data['Reviews']=$this->Courselist_model->getReviews($post_data);
			echo json_encode($data);				
		}			
	}
	public function deleteDiscussion($DiscussionId = NULL) {
		if(!empty($DiscussionId)) {					
			$data = $this->Courselist_model->deleteDiscussion($DiscussionId);
			echo json_encode($data);				
		}			
	}
	public function shareCourse()
		{				 		
		$post_data = json_decode(trim(file_get_contents('php://input')), true);		
		if ($post_data)
			{
					 $EmailToken = 'Share Course';

					 $this->db->select('Value');
					 $this->db->where('Key','EmailFrom');
					 $smtp1 = $this->db->get('tblmstconfiguration');	
					 foreach($smtp1->result() as $row) {
						 $smtpEmail = $row->Value;
					 }
					 $this->db->select('Value');
					 $this->db->where('Key','EmailPassword');
					 $smtp2 = $this->db->get('tblmstconfiguration');	
					 foreach($smtp2->result() as $row) {
						 $smtpPassword = $row->Value;
					 }
					 
					$config['protocol']=PROTOCOL;
					$config['smtp_host']=SMTP_HOST;
					$config['smtp_port']=SMTP_PORT;
					$config['smtp_user']=$smtpEmail;
					$config['smtp_pass']=$smtpPassword;
				 
					 $config['charset']='utf-8';
					 $config['newline']="\r\n";
					 $config['mailtype'] = 'html';							
					 $this->email->initialize($config);
			 
					 $query = $this->db->query("SELECT et.To,et.Subject,et.EmailBody,et.BccEmail,(SELECT GROUP_CONCAT(UserId SEPARATOR ',') FROM tbluser WHERE RoleId = et.To && ISActive = 1 && IsStatus = 0) AS totalTo,(SELECT GROUP_CONCAT(EmailAddress SEPARATOR ',') FROM tbluser WHERE RoleId = et.Cc && ISActive = 1 && IsStatus = 0) AS totalcc,(SELECT GROUP_CONCAT(EmailAddress SEPARATOR ',') FROM tbluser WHERE RoleId = et.Bcc && ISActive = 1 && IsStatus = 0) AS totalbcc FROM tblemailtemplate AS et LEFT JOIN tblmsttoken as token ON token.TokenId=et.TokenId WHERE token.TokenName = '".$EmailToken."' && et.IsActive = 1");
					
					 foreach($query->result() as $row){ 
						 if($row->To==4){
						 $body = $row->EmailBody;
						$body = str_replace("{ first_name }",$post_data['FirstName'],$body);
						$body = str_replace("{ last_name }",$post_data['LastName'],$body);
						$body = str_replace("{ course_name }",$post_data['courseName'],$body);
						$body = str_replace("{ course_skills }",$post_data['skills'],$body);
						$body = str_replace("{ course_price }",$post_data['price'],$body);
						$body = str_replace("{ link }",''.BASE_URL.'/course-detail/'.$post_data['CourseId'].'',$body);
						 $this->email->from($smtpEmail, 'LMS Admin');
						 $this->email->to($post_data['EmailAddress']);		
						 $this->email->subject($row->Subject);
						 $this->email->cc($row->totalcc);
						 $this->email->bcc($bcc);
						 $this->email->message($body);
						 if($this->email->send())
						 {
							$email_log = array(
								'From' => trim($smtpEmail),
								'Cc' => '',
								'Bcc' => '',
								'To' => trim($EmailAddress),
								'Subject' => trim($row->Subject),
								'MessageBody' => trim($body),
							);
							$res = $this->db->insert('tblemaillog',$email_log);	
							
						 }else
						 {
							 //echo json_encode("Fail");
						 }
					 }  
				 }
				 echo json_encode('Success'); 	
		}
		
	}
	
}