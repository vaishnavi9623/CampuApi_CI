<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Blog_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  public function get_Blogs($searchCategory = '',$value='')
  {
   $this->db->select('*');
    $this->db->from('blog');
    $this->db->where('image IS NOT NULL');
    $this->db->where('image IS NOT NULL');
    $this->db->where('title IS NOT NULL');
    if(!empty($searchCategory))
    {
    $this->db->where('categoryid',$searchCategory);
    }
    if(!empty($value))
    {
    $this->db->like('title',$value);
    }
    $this->db->order_by('id','desc');
	  //$this->db->limit(5);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->result();
    } else {
      return false;
    }
	
  }

  public function getLatestBlogs($collegeid)
  {
    $this->db->select('*');
    $this->db->from('blog');
    $this->db->where('t_status','1');
    $this->db->where('college_id',$collegeid);
    $this->db->order_by('created_date', 'DESC');
    $this->db->order_by('id', 'DESC');
    $this->db->limit(10);
    $query = $this->db->get();
    $result = $query->result();
    return $result;

  }


  public function getPopularBlogs($collegeid)
  {
    $this->db->select('*');
    $this->db->from('blog');
    $this->db->where('t_status','1');
    $this->db->where('views >', '10');
    $this->db->where('college_id',$collegeid);
    $this->db->order_by('views', 'DESC');
    $this->db->order_by('created_date', 'DESC');
    $this->db->order_by('id', 'DESC');
    $this->db->limit(10);
    $query = $this->db->get();
    $result = $query->result();
    return $result;

  }

  public function getBlogsDetails($blogId)
  {
    $this->db->select('b.id as blog_id,b.categoryid,bc.name as category_name,b.title,b.post_url, b.image,b.description,b.post_rate_date,MONTH( post_rate_date) as month,DAY( post_rate_date) as day,YEAR( post_rate_date) as year,SUBSTRING(`description`,1,90) as short_desc');
    $this->db->join('blog_category bc', 'bc.id = b.categoryid','left');
    $this->db->where('b.id', $blogId);
		return $this->db->get('blog b')->result();
  }

  public function increment_view($blogId = '')
  {
    if (!empty($blogId)) {
      $this->db->where('id', $blogId);
        $this->db->set('views', 'views+1', FALSE);
        $this->db->set('post_rate_date', date('Y-m-d H:i:s'));
        $this->db->update('blog');
       
  }
  }
  public function relatedBlogs($id,$blog_id) {

		$list = array();
		$Array = explode(',', $id);
		foreach ($Array as $categoryid)
		{
			if( count($list) < 5) {
				$this->db->select('id,title,post_url,categoryid,image,post_rate_date');
				$this->db->where("FIND_IN_SET('$categoryid',categoryid) !=", 0);
				$this->db->where('id!= ', $blog_id);
				$this->db->order_by('id','DESC');
				$this->db->where('t_status','1');
				$this->db->limit(5);
				$data=$this->db->get('blog')->result();
				if(!empty($data)) {
						foreach($data as $value){
						    if(!in_array($value, $list, true)) {
						        $list[]=$value;
						    }
						}
				}
			}
			
		}
		return $list;
	}
  
  public function getBlogCategory()
  {
    $this->db->select('id,name,post_url');
		$this->db->where('status','1');
		return $this->db->get('blog_category')->result_array();
  }
}
