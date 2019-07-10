<?php

class InstructorFollowers_model extends CI_Model
{

	public function get_Followerdata($FollowerId = Null)
	{
		try {
			if ($FollowerId) {
				$this->db->select('*');
				$this->db->where('UserId', $FollowerId);
				$result = $this->db->get('tbluser u');

				$db_error = $this->db->error();
				if (!empty($db_error) && !empty($db_error['code'])) {
					throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
					return false; // unreachable return statement !!!
				}
				$Follower_data = array();
				foreach ($result->result() as $row) {
					$Follower_data = $row;
				}
				return $Follower_data;
			} else {
				return false;
			}
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
			return false;
		}
	}


	function getAllFollowers($InstructorId = NULL)
	{
		try {
			if ($InstructorId) {
				$result = $this->db->query(
					"SELECT u.UserId,CONCAT(u.FirstName,' ',u.LastName) as Name,u.Title,u.EmailAddress,u.PhoneNumber FROM tbluser u, tblinstructorfollowers i
		WHERE FIND_IN_SET(u.UserId, i.FollowerUserId) and i.InstructorUserId=" . $InstructorId
				);

				$db_error = $this->db->error();
				if (!empty($db_error) && !empty($db_error['code'])) {
					throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
					return false; // unreachable return statement !!!
				}
				$res = array();
				if ($result->result()) {
					$res = $result->result();
				}
				return $res;
			} else {
				return false;
			}
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
			return false;
		}
	}



	function followInstructor($post_followInstructor)
	{
		try {
			if ($post_followInstructor) {
				$InstructorId = $post_followInstructor['InstructorId'];
				$LearnerId = $post_followInstructor['LearnerId'];

				$this->db->select('tif.InstructorUserId,tif.FollowerUserId');
				$this->db->from('tblinstructorfollowers tif');
				$this->db->where('tif.InstructorUserId', $InstructorId);
				$query = $this->db->get();
				if ($query->num_rows() > 0) {
					$result = $query->result();
					$FollowerUserId = $result[0]->FollowerUserId;
					if ($FollowerUserId != '') {
						$FollowerIds  =  $FollowerUserId . ',' . $LearnerId;
					} else {
						$FollowerIds = $LearnerId;
					}
					$data = array(
						'InstructorUserId' => $InstructorId,
						'FollowerUserId' => $FollowerIds,
						'IsActive' => 1
					);
					$this->db->where('InstructorUserId', $InstructorId);
					$res = $this->db->update('tblinstructorfollowers', $data);
				} else {
					$data = array(
						'InstructorUserId' => $InstructorId,
						'FollowerUserId' => $LearnerId,
						'IsActive' => 1
					);
					$res = $this->db->insert('tblinstructorfollowers', $data);
				}


				$db_error = $this->db->error();
				if (!empty($db_error) && !empty($db_error['code'])) {
					throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
					return false; // unreachable return statement !!!
				}
				if ($res) {
					$log_data = array(
						'UserId' => trim($post_followInstructor['LearnerId']),
						'Module' => 'Instructor',
						'Activity' => 'Follow Instructor'
					);
					$log = $this->db->insert('tblactivitylog', $log_data);
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
			return false;
		}
	}

	function unfollowInstructor($post_unfollowInstructor)
	{
		try {
			if ($post_unfollowInstructor) {
				$InstructorId = $post_unfollowInstructor['InstructorId'];
				$LearnerId = $post_unfollowInstructor['LearnerId'];

				$res = $this->db->query("UPDATE tblinstructorfollowers SET FollowerUserId=replace(FollowerUserId,'" . $LearnerId . ",',''),FollowerUserId=replace(FollowerUserId,'," . $LearnerId . "',''),FollowerUserId=replace(FollowerUserId,'" . $LearnerId . "','') WHERE InstructorUserId=" . $InstructorId);

				$db_error = $this->db->error();
				if (!empty($db_error) && !empty($db_error['code'])) {
					throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
					return false; // unreachable return statement !!!
				}
				if ($res) {
					$log_data = array(
						'UserId' => trim($post_unfollowInstructor['LearnerId']),
						'Module' => 'Instructor',
						'Activity' => 'Unfollow Instructor'
					);
					$log = $this->db->insert('tblactivitylog', $log_data);
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
			return false;
		}
	}
	function getFollowerDetails($post_data)
	{
		try {
			if ($post_data) {
				$this->db->select('FIND_IN_SET(' . $post_data['LearnerId'] . ',tif.FollowerUserId) as flag,tif.FollowerUserId,tif.InstructorUserId');
				$this->db->from('tblinstructorfollowers tif');
				//$this->db->join('tbluser u','tif.FollowerUserId = u.UserId','inner');
				$this->db->where('tif.InstructorUserId', $post_data['InstructorId']);
				$result = $this->db->get();

				$db_error = $this->db->error();
				if (!empty($db_error) && !empty($db_error['code'])) {
					throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
					return false; // unreachable return statement !!!
				}
				$res = array();
				$total = [];
				if ($result->result()) {
					foreach ($result->result() as $row) {
						$res['flag'] = $row->flag;
						if ($row->FollowerUserId != '') {
							$total = explode(',', $row->FollowerUserId);
						}
						$res['totalFollowers'] = count($total);
						return $res;
					}
				} else {
					return null;
				}
			} else {
				return false;
			}
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
			return false;
		}
	}
	function getInstructorDetails($post_data)
	{
		try {
			if ($post_data) {
				$this->db->select('u.UserId,u.FirstName,u.LastName,u.ProfileImage,el.Education,u.Biography');
				$this->db->from('tbluser u');
				$this->db->join('tbluserdetail ud', 'ud.UserDetailId = u.UserDetailId', 'left');
				$this->db->join('tblmsteducationlevel el', 'el.EducationLevelId = ud.EducationLevelId', 'left');
				$this->db->where('u.UserId', $post_data['InstructorId']);
				$result = $this->db->get();

				$db_error = $this->db->error();
				if (!empty($db_error) && !empty($db_error['code'])) {
					throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
					return false; // unreachable return statement !!!
				}
				$res = array();
				foreach ($result->result() as $row) {
					$res = $row;
				}
				return $res;
			} else {
				return false;
			}
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
			return false;
		}
	}
	function getActiveCourses($post_data)
	{
		try {
			if ($post_data) {
				$this->db->select('c.CourseId,c.CourseFullName,c.Description,rs.InstructorId,rs.FilePath,(SELECT ROUND(AVG(Rating),1) from tblcoursereview where CourseId = c.CourseId) as reviewavg');
				$this->db->join('tblcoursesession cs', 'cs.CourseSessionId = tci.CourseSessionId', 'left');
				$this->db->join('tblcourse c', 'c.CourseId = cs.CourseId', 'left');
				$this->db->join('tblresources rs', 'rs.ResourcesId = c.CourseImageId', 'left');
				$this->db->where('tci.UserId', $post_data['InstructorId']);
				$this->db->where('cs.PublishStatus!=', 0);
				$this->db->group_by('c.CourseId');
				$this->db->from('tblcourseinstructor tci');
				$result = $this->db->get();

				$db_error = $this->db->error();
				if (!empty($db_error) && !empty($db_error['code'])) {
					throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
					return false; // unreachable return statement !!!
				}
				$res = array();
				if ($result->result()) {
					$res = $result->result();
				}
				return $res;
			} else {
				return false;
			}
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
			return false;
		}
	}

	function getAllInstructors($post_data)
	{
		try {
			if ($post_data) {
				$this->db->select('FIND_IN_SET(' . $post_data['LearnerId'] . ',tif.FollowerUserId) as flag,u.UserId,u.FirstName,u.LastName,u.ProfileImage,u.Biography,tif.FollowerUserId,tif.InstructorUserId');
				$this->db->join('tblinstructorfollowers tif', 'tif.InstructorUserId = u.UserId', 'left');
				$this->db->from('tbluser u');
				$this->db->where('u.RoleId', 3);
				$result = $this->db->get();
				$q = $this->db->last_query();

				$db_error = $this->db->error();
				if (!empty($db_error) && !empty($db_error['code'])) {
					throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
					return false; // unreachable return statement !!!
				}
				$res = array();


				foreach ($result->result() as $row) {
					$total = [];
					if ($row->FollowerUserId != '') {
						$total = explode(',', $row->FollowerUserId);
						$row->totalFollowers = count($total);
					} else {
						$row->totalFollowers = 0;
					}
					//following
					if ($row->UserId != '') {
						$result = $this->db->query('SELECT FIND_IN_SET(' . $row->UserId . ', tif.FollowerUserId) as flag FROM `tblinstructorfollowers` `tif`');
						// $res = array();
						$count = 0;
						if ($result->result()) {
							foreach ($result->result() as $row1) {
								if ($row1->flag != 0) {
									$count = $count + 1;
								}
							}
						}
						$row->totalFolloings = $count;
					} else {
						$row->totalFolloings = 0;
					}
					array_push($res, $row);
				}
				return $res;
			} else {
				return false;
			}
		} catch (Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
			return false;
		}
	}


}
