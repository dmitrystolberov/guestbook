<?php

class Comment extends BaseModel
{

    const STATUS_NEW = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;
    const TYPE_ID_TEXT = 1;
    const TYPE_ID_IMAGE = 2;

    public $id;
    public $user_id;
    public $text;
    public $image_id;
    public $status;
    public $comment_type_id;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return User the static model class
     */
    public static function model($forceNew = false, $className = __CLASS__)
    {
        return parent::model($forceNew, $className);
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return 'comments';
    }

    /**
     * @param null $status
     * @return PDOStatement
     */
    public function findAll($status = null)
    {
        $where = (!empty($status) && in_array($status, [
            self::STATUS_NEW, self::STATUS_APPROVED, self::STATUS_REJECTED
            ])) ? ' WHERE status = ' . $status : '';
        $sql = 'SELECT c.id, c.comment_type_id, c.text, c.status, i.image_name, u.login, c.created_date FROM ' . $this->getTableName() . ' AS c
            LEFT JOIN ' . Image::model()->getTableName() .  ' AS i ON c.image_id=i.id
            INNER JOIN ' . User::model()->getTableName() . ' AS u ON c.user_id = u.id'
            . $where . ' ORDER BY c.id DESC';
        $statement = $this->execute($sql);
        $res =  $statement->fetchAll(PDO::FETCH_OBJ);
        return $res;
    }

    /**
     * Returns description for statuses
     * @param int
     * @return string
     */
    public static function getStatusTextByStatus($status)
    {
        switch ($status) {
            case self::STATUS_APPROVED:
                return 'Approved';
                break;
            case self::STATUS_REJECTED:
                return 'Rejected';
                break;
            default:
                return 'New';
                break;
        }
    }
}