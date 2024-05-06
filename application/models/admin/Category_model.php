<?php
/**
 * category Model
 *
 * @category   Models
 * @package    Admin
 * @subpackage Category
 * @version    1.0
 * @author     Vaishnavi Badabe
 * @created    18 JAN 2024
 * 
 * Class category_model handles all category-related operations.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {

    private $table = 'category';
	private $Newtable = 'academic_categories';

    public function __construct() {
        parent::__construct();
    }

	/**
     * Count all category in the table.
     *
     * @return int The total number of category.
     */
    public function countAllCategory($type)
    {
		$this->db->where("type", $type);
    	$query = $this->db->get($this->table);
    	return $query->num_rows();
    }

	/**
     * Count filtered category based on the search term.
     *
     * @param string $search The search term.
     * @return int The number of filtered category.
     */
    public function countFilteredCategory($search,$type)
    {
		$this->db->where("type", $type);
        $this->db->like('catname', $search);
        // $this->db->or_like('type', $search);
		
        return $this->db->get($this->table)->num_rows();
    }

	/**
     * Get filtered category.
     *
     * @param string $search The search term.
     * @param int    $start  The starting index for pagination.
     * @param int    $limit  The number of records to retrieve.
     * @param string $order  The column to order by.
     * @param string $dir    The direction of sorting.
     * @return array The list of filtered and paginated users with additional information.
     */

	 public function getFilteredCategory($search, $start, $limit, $order, $dir,$type)
	 {
		 $this->db->select("*");
		 $this->db->from($this->table);
 		$this->db->where("type", $type);
		 $this->db->group_start(); 
		 $this->db->like('catname', $search);
		 $this->db->or_like('type', $search);
		 $this->db->group_end(); 
 
		 $this->db->order_by($order, $dir);
		 $this->db->limit($limit, $start);
		 return $this->db->get()->result();
		 
	 }

	 /**
     * Get all Category with filtering, ordering, and pagination.
     *
     * @param int    $start  The starting index for pagination.
     * @param int    $limit  The number of records to retrieve.
     * @param string $order  The column to order by.
     * @param string $dir    The direction of sorting.
     * @return array The list of filtered and paginated Category.
     */

	 public function getAllCategory($start, $limit, $order, $dir,$type)
	 {
		 $this->db->select("*");
		 $this->db->from($this->table);
 		 $this->db->where("type", $type);
		 $this->db->order_by($order, $dir);
		 $this->db->limit($limit, $start);
		 return $this->db->get()->result();
		
	 }

	 /**
     * Check if an Category exists .
     *
     * @param string $CatName The Category  to check.
     * @return int The count of Category .
     */
	public function chkIsExtists($CatName,$type,$MenuOrder)
    {
        $this->db->where("catname", $CatName);
		$this->db->where("type", $type);
        $this->db->where("menuorder", $MenuOrder);

        $query = $this->db->get($this->table);
        return $query->num_rows();
    }

	/**
     * Insert details for all Category into the database.
     *	
     * @param array $data The data to be inserted.
     * @return bool True if data insertion is successful, otherwise false.
     */
    public function insertCategoryDetails($data)
    {
        $query = $this->db->insert($this->table, $data);
		return $this->db->insert_id();
    }

	/**
     * Get the details of a Category by ID.
     *
     * @param string $id The ID to retrieve Category details.
     * @return object The details of the Category as an object.
     */
    public function getCategoryDetailsById($id,$type)
    {
        $this->db->where("id", $id);
		$this->db->where("type", $type);
        return $this->db->get($this->table)->row();
    }

	function chkWhileUpdate($id, $data,$type,$MenuOrder) {
		$this->db->where('catname', $data);
		$this->db->where("type", $type);
        $this->db->where("menuorder", $MenuOrder);
		$this->db->where("id !=", $id);
		$query = $this->db->get($this->table);
		return $query->num_rows();
	}

	/**
     * Update the details of a Category by ID.
     *
     * @param string $id   The ID of the Category to be updated.
     * @param array  $data An associative array containing the data to be updated.
     * @return bool        True if the update operation is successful, otherwise false.
     */
    public function updateCategoryDetails($id, $data)
    {
        $this->db->where("id", $id);
        $query = $this->db->update($this->table, $data);
        return $query;
    }

	 /**
     * Delete the details of a Category by ID.
     *
     * @param string $id   The ID of the Category to be deleted.
     * @return bool        True if the delete operation is successful, otherwise false.
     */
    public function deleteCategoryDetails($id)
    {
        $this->db->where("id", $id);
        $query = $this->db->delete($this->table);
        return $query;
    }

	/**
     * Get the details of a Category by type.
     *
     * @param string $id The ID to retrieve Category details.
     * @return object The details of the Category as an object.
     */
    public function getCategoryListByType($type)
    {
		$this->db->where("type", $type);
        return $this->db->get($this->table)->result();
    }

	public function getCategoryForCourse()
	{
		$this->db->where("type", 'college');
        return $this->db->get($this->table)->result();
	}
	public function getAcCategoryForCourse()
	{
        return $this->db->get($this->Newtable)->result();
	}

    public function getSubCategoryByCatId($catId,$acCatId)
    {
        $this->db->where("parent_category", $catId);
        $this->db->where("academic_category", $acCatId);

        return $this->db->get('sub_category')->result();
    }


    public function getSubCategory($search_category = NULL,$categoryId)
    {
		$this->db->select("*");
		$this->db->from('sub_category');
        $this->db->where('parent_category',$categoryId);
		if ($search_category !== NULL) {
			$this->db->like('name', $search_category);
		}
		$this->db->limit(10);
		$query = $this->db->get();
        // echo $this->db->last_query();exit;

        return $query->result();

	}

    public function getCategories($search_category)
    {
        $this->db->select("*");
		$this->db->from('category'); 
		if ($search_category !== NULL) {
			$this->db->like('catname', $search_category);
		}
		$this->db->limit(10);
		return $this->db->get()->result();

    }

}
