<?php
/**
 * Cutoff Model
 *
 * @category   Models
 * @package    Admin
 * @subpackage Cutoff
 * @version    1.0
 * @author     Vaishnavi Badabe
 * @created    24 APRIL 2024
 * 
 * Class Cutoff_model handles all Cutoff-related operations.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cutoff_model extends CI_Model {

    private $KCETtable = 'cutoff_kcet';

    public function __construct() {
        parent::__construct();
    }

    public function insertKCETCutoff($dataArray)
    {
        $query = $this->db->insert($this->KCETtable, $dataArray);
		return $this->db->insert_id();
    }


    public function getKCETCutOffByCollegeId($college_id)
    {
        $this->db->select('ck.*, c.title as collegename, ct.catname as categoryname, cs.name as coursename');
        $this->db->from('cutoff_kcet ck');
        $this->db->join('college c', 'c.id = ck.college_id', 'left');
        $this->db->join('category ct', 'ct.id = ck.category_id', 'left');
        $this->db->join('courses cs', 'cs.id = ck.course_id', 'left');
        $this->db->where('ck.college_id', $college_id);

        $query = $this->db->get();
        $result = $query->result();
        return $result;

    }

    public function checkIsExists($dataArray)
    {
        $this->db->where('college_id', $dataArray['college_id']);
        $this->db->where('round', $dataArray['round']);
        $this->db->where('course_id', $dataArray['course_id']);
        $this->db->where('category_id', $dataArray['category_id']);
        $this->db->where('year', $dataArray['year']);
        $query = $this->db->get('cutoff_kcet');
        $result = $query->num_rows();
        return $result;
    }

    public function updateKCETCutoff($dataArray)
    {
        $this->db->where('college_id', $dataArray['college_id']);
        $this->db->where('round', $dataArray['round']);
        $this->db->where('course_id', $dataArray['course_id']);
        $this->db->where('category_id', $dataArray['category_id']);
        $this->db->where('year', $dataArray['year']);
        $this->db->update('cutoff_kcet', $dataArray);
        return $this->db->affected_rows();
    }


    public function countAllKCETCut()
    {
        $query = $this->db->get($this->KCETtable);
    	return $query->num_rows();
    }

    public function getFilteredKCETCut($search, $start, $limit, $orderColumn, $orderDir)
    {
        $this->db->select('ck.year,ck.id,ck.round, c.title as collegename, ct.catname as categoryname, cs.name as coursename');
        $this->db->from('cutoff_kcet ck');
        $this->db->join('college c', 'c.id = ck.college_id', 'left');
        $this->db->join('category ct', 'ct.id = ck.category_id', 'left');
        $this->db->join('courses cs', 'cs.id = ck.course_id', 'left');
        $this->db->where('ck.is_deleted', 0);

        $this->db->group_start(); 
        $this->db->like('c.title', $search);
        $this->db->or_like('ct.catname', $search);
        $this->db->or_like('cs.name', $search);
        $this->db->group_end(); 
        $this->db->order_by($orderColumn, $orderDir);
		$this->db->limit($limit, $start);
		return $this->db->get()->result();
    }

    public function countFilteredKCETCut($search)
    {
        $this->db->from('cutoff_kcet ck');
        $this->db->join('college c', 'c.id = ck.college_id', 'left');
        $this->db->join('category ct', 'ct.id = ck.category_id', 'left');
        $this->db->join('courses cs', 'cs.id = ck.course_id', 'left');

        $this->db->where('ck.is_deleted', 0);
        $this->db->group_start(); 
        $this->db->like('c.title', $search);
        $this->db->or_like('ct.catname', $search);
        $this->db->or_like('cs.name', $search);
        $this->db->group_end(); 		
        return $this->db->get()->num_rows();
    }

    public function getAllKCETCut($start, $limit, $orderColumn, $orderDir)
    {
        $this->db->select('ck.*, c.title as collegename, ct.catname as categoryname, cs.name as coursename');
        $this->db->from('cutoff_kcet ck');
        $this->db->join('college c', 'c.id = ck.college_id', 'left');
        $this->db->join('category ct', 'ct.id = ck.category_id', 'left');
        $this->db->join('courses cs', 'cs.id = ck.course_id', 'left');
        $this->db->where('ck.is_deleted', 0);
        $this->db->order_by($orderColumn, $orderDir);
		 $this->db->limit($limit, $start);
		 return $this->db->get()->result();
    }


  public function viewMoreKcet($id)
    {
        $this->db->select('ck.`id`, `round`,  `year`, `1G`, `1H`, `1K`, `1KH`, `1R`, `1RH`, `2AG`, `2AH`, `2AK`, `2AKH`, `2AR`, `2ARH`, `2BG`, `2BH`, `2BK`, `2BKH`, `2BR`, `2BRH`, `2BRG`, `3AG`, `3AH`, `3AK`, `3AKH`, `3AR`, `3ARH`, `3BG`, `3BH`, `3BK`, `3BKH`, `3BR`, `3BRH`, `GM`, `GMH`, `GMK`, `GMKH`, `GMR`, `GMRH`, `SCG`, `SCH`, `SCK`, `SCKH`, `SCR`, `SCRH`, `STG`, `STH`, `STK`, `STKH`, `STR`, `STRH`, c.title as collegename, ct.catname as categoryname, cs.name as coursename');
        $this->db->from('cutoff_kcet ck');
        $this->db->join('college c', 'c.id = ck.college_id', 'left');
        $this->db->join('category ct', 'ct.id = ck.category_id', 'left');
        $this->db->join('courses cs', 'cs.id = ck.course_id', 'left');
        $this->db->where('ck.id', $id);
        $this->db->where('ck.is_deleted', 0);
        $query = $this->db->get();
    
    if ($query->num_rows() > 0) {
        $result = $query->result();
        foreach ($result as $row) {
            $rowArray = [];
            foreach ($row as $column_name => $value) {
                $rowArray[] = array('label' => $column_name, 'value' => $value);
            }
            $newArr[] = $rowArray;
            return $newArr;

            // print_r($newArr);exit;
        }
    } else {
        echo "0 results";
    }
    
}


    public function deletedKCET($id)
    {
        $dataArray = ['is_deleted'=>1];
        $this->db->where('id', $id);
        $this->db->update('cutoff_kcet', $dataArray);
        return $this->db->affected_rows();
    }

    public function getDetailsById($id)
    {
        $this->db->select('ck.*, c.title as collegename, ct.catname as categoryname, cs.name as coursename');
        $this->db->from('cutoff_kcet ck');
        $this->db->join('college c', 'c.id = ck.college_id', 'left');
        $this->db->join('category ct', 'ct.id = ck.category_id', 'left');
        $this->db->join('courses cs', 'cs.id = ck.course_id', 'left');
        $this->db->where('ck.is_deleted', 0);
        $this->db->where('ck.id', $id);

        // $this->db->order_by($orderColumn, $orderDir);
		//  $this->db->limit($limit, $start);
		 return $this->db->get()->result();
    }
}