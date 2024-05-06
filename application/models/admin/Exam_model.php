<?php
/**
 * exam Model
 *
 * @category   Models
 * @package    Admin
 * @subpackage exam
 * @version    1.0
 * @author     Vaishnavi Badabe
 * @created    26 JAN 2024
 *
 * Class exam_model handles all exam-related operations.
 */

defined("BASEPATH") or exit("No direct script access allowed");

class Exam_model extends CI_Model
{
    private $table = "exams";
    private $imgtable = "gallery";

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Count all exam in the table.
     *
     * @return int The total number of exam.
     */
    public function countAllExam($userId, $userType)
    {
        if ($userType == 14) {
            $this->db->where("created_by", $userId);
            return $this->db->count_all_results($this->table);
        } else {
            return $this->db->count_all($this->table);
        }
    }

    /**
     * Count filtered exam based on the search term.
     *
     * @param string $search The search term.
     * @return int The number of filtered exam.
     */
    public function countFilteredExam($search, $userId, $userType)
    {
        if ($userType == 14) {
            $this->db->where("e.created_by", $userId);
        }
        $this->db->like("e.title", $search);
        $this->db->or_like("c.catname", $search);
        $this->db->or_like("c.status", $search);
        $this->db->join("category c", "c.id = e.categoryid", "left");
        return $this->db->get($this->table . " e")->num_rows();
    }

    /**
     * Get filtered exam.
     *
     * @param string $search The search term.
     * @param int    $start  The starting index for pagination.
     * @param int    $limit  The number of records to retrieve.
     * @param string $order  The column to order by.
     * @param string $dir    The direction of sorting.
     * @return array The list of filtered and paginated exam with additional information.
     */

    public function getFilteredExam(
        $search,
        $start,
        $limit,
        $order,
        $dir,
        $userId,
        $userType
    ) {
        $this->db->select(
            'e.*, CONCAT(u.f_name, " ", u.l_name) as created_by_name, CONCAT(u1.f_name, " ", u1.l_name) as updated_by_name'
        );
        $this->db->select(
            "(SELECT GROUP_CONCAT(catname) FROM category c WHERE FIND_IN_SET(c.id, e.categoryid)) as category",
            false
        ); // FALSE to prevent escaping
        $this->db->from($this->table . " e");
        $this->db->join(
            "category c",
            "FIND_IN_SET(c.id, e.categoryid)",
            "left"
        );
        $this->db->join("users u", "u.id = e.created_by", "left");
        $this->db->join("users u1", "u1.id = e.updated_by", "left");

        if ($userType == 14) {
            $this->db->where("e.created_by", $userId);
        }

        $this->db->group_start();
        $this->db->like("e.title", $search);
        $this->db->or_like("c.catname", $search);
        $this->db->or_like("c.status", $search);
        $this->db->group_end();
        $this->db->group_by("e.id");
        $this->db->order_by($order, $dir);
        $this->db->limit($limit, $start);

        return $this->db->get()->result();
    }

    /**
     * Get all exam with filtering, ordering, and pagination.
     *
     * @param int    $start  The starting index for pagination.
     * @param int    $limit  The number of records to retrieve.
     * @param string $order  The column to order by.
     * @param string $dir    The direction of sorting.
     * @return array The list of filtered and paginated exam.
     */

    public function getAllExam($start, $limit, $order, $dir, $userId, $userType)
    {
        $this->db->select(
            'e.*, CONCAT(u.f_name, " ", u.l_name) as created_by_name, CONCAT(u1.f_name, " ", u1.l_name) as updated_by_name'
        );
        $this->db->select(
            "(SELECT GROUP_CONCAT(catname) FROM category c WHERE FIND_IN_SET(c.id, e.categoryid)) as category",
            false
        ); // FALSE to prevent escaping
        $this->db->from($this->table . " e");
        $this->db->join(
            "category c",
            "FIND_IN_SET(c.id, e.categoryid)",
            "left"
        );
        $this->db->join("users u", "u.id = e.created_by", "left");
        $this->db->join("users u1", "u1.id = e.updated_by", "left");

        if ($userType == 14) {
            $this->db->where("e.created_by", $userId);
        }

        // Grouping by 'e.id' to avoid unintended grouping issues
        $this->db->group_by("e.id");

        $this->db->order_by($order, $dir);
        $this->db->limit($limit, $start);

        return $this->db->get()->result();
    }

    /**
     * Check if an exam exists .
     *
     * @param string $name to check.
     * @return int The count of exam .
     */
    public function chkIfExists($provider_name)
    {
        $this->db->where("title", $provider_name);
        $query = $this->db->get($this->table);
        return $query->num_rows();
    }

    /**
     * Insert details for exam into the database.
     *
     * @param array $data The data to be inserted.
     * @return bool True if data insertion is successful, otherwise false.
     */
    public function insertExamDetails($data)
    {
        $query = $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Check if an exam exists while updatte.
     *
     * @param string $data,$id The exam  to check.
     * @return int The count of exam .
     */
    function chkWhileUpdate($id, $name)
    {
        $this->db->where("title", $name);
        $this->db->where("id !=", $id);
        $query = $this->db->get($this->table);
        return $query->num_rows();
    }

    /**
     * Update the details of a exam by ID.
     *
     * @param string $id   The ID of the exam to be updated.
     * @param array  $data An associative array containing the data to be updated.
     * @return bool        True if the update operation is successful, otherwise false.
     */
    public function updateExamDetails($id, $data)
    {
        $this->db->where("id", $id);
        $query = $this->db->update($this->table, $data);
        return $query;
    }
    /**
     * Get the details of a exam by ID.
     *
     * @param string $id The ID to retrieve exam details.
     * @return object The details of the exam as an object.
     */
    public function getExamDetailsById($id)
{
    $this->db->select("e.id AS exams_id, e.*, GROUP_CONCAT(cat.catname) AS category_names");
    $this->db->from($this->table . " AS e");
    $this->db->join('category AS cat', 'FIND_IN_SET(cat.id, e.categoryid)', 'left');
    $this->db->where("e.id", $id);
    $this->db->group_by("e.id");
    $this->db->order_by("e.id");
    return $this->db->get()->row();
}


    public function getExamImgDetailsById($id)
    {
        $this->db->select("*");
        $this->db->from("gallery");
        $this->db->where("postid", $id);
        $this->db->where("type", "exams");
        return $this->db->get()->result();
    }

    /**
     * Delete the details of a exam by ID.
     *
     * @param string $id   The ID of the exam to be deleted.
     * @return bool        True if the delete operation is successful, otherwise false.
     */
    public function deleteExam($id)
    {
        $this->db->where("id", $id);
        $query = $this->db->delete($this->table);
        return $query;
    }

    /**
     * Insert docs details for exam into the database.
     *
     * @param array $data The data to be inserted.
     * @return bool True if data insertion is successful, otherwise false.
     */
    public function insertExamDocsDetails($data)
    {
        $query = $this->db->insert($this->imgtable, $data);
        $imageId["imageId"] = $this->db->insert_id();
        return $imageId;
    }
    public function updateExamDocsDetails($id, $postid, $Arr)
    {
        $this->db->where("id", $id);
        $this->db->where("postid", $postid);
        $query = $this->db->update($this->imgtable, $Arr);
        return $query;
    }

    public function deleteDoc($Id)
    {
        $this->db->where("id", $Id);
        return $this->db->delete($this->imgtable);
    }

    public function getExams($searchExams = null)
    {
        $this->db->select("id as exams_id, title");
        $this->db->from($this->table);
        $this->db->where("status", "1");

        if (!empty($searchExams)) {
            $this->db->like("title", $searchExams);
        }

        $this->db->limit(10);

        return $this->db->get()->result();
    }

    public function updateExamsDocs($examId, $Arr)
    {
        $this->db->where("id", $examId);
        $query = $this->db->update($this->table, $Arr);
        return $query;
    }
}
