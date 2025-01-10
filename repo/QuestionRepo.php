<?php

require_once __DIR__ . "/../utils/AppConstants.php";
require_once __DIR__ . "/../assets/dbconn.php";
require_once __DIR__ . "/QuestionImageMappingRepo.php";
require_once __DIR__ . "/../functions.php";

class QuestionRepo
{
    public $tableName;
    public $conn, $now;
    private $questionImageMappingRepo;


    public function __construct()
    {
        global $conn, $now;
        $this->tableName = AppConstants::QUESTIONS_TABLE;
        $this->conn = $conn;
        $this->now = $now;
        $this->createTable();
        $this->questionImageMappingRepo = new QuestionImageMappingRepo();
    }

    private function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTs ' . $this->tableName . '  (
        questionId varchar(255) not null,
        question text not null,
        option1 varchar(1000) ,
        option2 varchar(1000) ,
        option3 varchar(1000) ,
        option4 varchar(1000) ,
        categoryId varchar(255) not null,
        correctAns varchar(255) not null,
        userId int not null,
        marks float not null,
        questionDatetime varchar(45) not null,
        questionVisibility int default 0,
        questionStatus int default 1,
        haveImages int not null default 0,
        isDeleted int not null default 0,
        deletedBy int ,
        deletedOn varchar(50),
        primary key (questionId),
        constraint FK_question_cat Foreign Key (categoryId) references ' . AppConstants::CATEGORY_TABLE . ' (categoryId) 

        )';
        $res = mysqli_query($this->conn, $sql);
        if ($res) {
            return true;
        }
        return false;
    }


    function save($model)
    {
        $sql = "INSERT INTO " . $this->tableName . " (questionId, question, option1, option2, option3, option4, correctAns, userId, questionDatetime, questionStatus, marks, categoryId, haveImages) 
        values ('" . getUUID() . "', '$model->question', '$model->option1', '$model->option2', '$model->option3', '$model->option4',  '$model->correctAns', $model->userId, '$this->now', 1, $model->marks, '$model->categoryId', $model->haveImages)";
        try {
            $res = mysqli_query($this->conn, $sql);
        } catch (Exception $e) {
            echo sendResponse(false, 500, $e->getMessage());
        }
        if ($res) {
            return true;
        } else {
            return false;
        }
    }


    function getAll()
    {
        $sql = "SELECT A.*, B.* FROM " . $this->tableName . " A inner join " . AppConstants::CATEGORY_TABLE . " B on A.categoryId = B.categoryId where A.isDeleted = 0";
        $data = [];
        $images = [];
        $res = mysqli_query($this->conn, $sql);
        while ($row = mysqli_fetch_assoc($res)) {
            if ($row['haveImages'] == 1) {
                $images = $this->questionImageMappingRepo->getAllByQuestionId($row['questionId']);
            }
            $row['images'] = $images;
            $data[] = $row;
        }

        return $data;
    }

    function getAllByUserId($userId)
    {
        $sql = "SELECT A.*, B.* FROM " . $this->tableName . " A inner join " . AppConstants::CATEGORY_TABLE . " B on A.categoryId = B.categoryId where A.userId = $userId and A.isDeleted = 0";
        $images = [];
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while ($row = mysqli_fetch_assoc($res)) {
            if ($row['haveImages'] == 1) {
                $images = $this->questionImageMappingRepo->getAllByQuestionId($row['questionId']);
            }
            $row['images'] = $images;
            $data[] = $row;
        }

        return $data;
    }

    function getLatestQuestionByUserId($userId)
    {
        $sql = "SELECT A.*, B.* FROM " . $this->tableName . " A inner join " . AppConstants::CATEGORY_TABLE . " B on A.categoryId = B.categoryId where A.userId = '$userId' and A.isDeleted = 0 order by A.questionDatetime desc limit 0,1";
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function getById($id)
    {
        $sql = "SELECT A.*, B.* FROM " . $this->tableName . " A inner join " . AppConstants::CATEGORY_TABLE . " B on A.categoryId = B.categoryId where A.questionId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($res);
    }

    function deleteById($id)
    {
        $sql = "DELETE FROM " . $this->tableName . " where questionId = '$id'";
        $res = mysqli_query($this->conn, $sql);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    function getQuestionsWhichAreNotMappedInQuiz($quizId, $userId)
    {
        $sql = "SELECT A.* FROM " . $this->tableName . " A where A.questionId not in (SELECT B.questionId from " . AppConstants::QUIZ_QUESTION_RELATION . " B where B.quizId = '$quizId' and B.isDeleted = 0) and A.userId = $userId and A.isDeleted = 0";
        $data = [];
        try {
            $res = mysqli_query($this->conn, $sql);
        } catch (Exception $e) {
            echo sendResponse(false, 500, $e->getMessage());
        }
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        return $data;
    }

    function getQuestionsWhichAreNotMappedInQuizByCategoryId($quizId, $userId, $categoryId)
    {
        $sql = "SELECT A.* FROM " . $this->tableName . " A where A.questionId not in (SELECT B.questionId from " . AppConstants::QUIZ_QUESTION_RELATION . " B where B.quizId = '$quizId' and B.isDeleted=0) and A.userId = $userId and A.categoryId = '$categoryId' and A.isDeleted = 0";
        $data = [];
        try {
            $res = mysqli_query($this->conn, $sql);
        } catch (Exception $e) {
            echo sendResponse(false, 500, $e->getMessage());
        }
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        return $data;
    }

    public function getQuestionsByCategoryIdAndUserId($categoryId, $userId)
    {
        $sql = "SELECT A.*, B.* FROM " . $this->tableName . " A inner join " . AppConstants::CATEGORY_TABLE . " B on A.categoryId = B.categoryId where A.userId = $userId and A.categoryId = '$categoryId' and A.isDeleted = 0";
        $images = [];
        $data = [];
        $res = mysqli_query($this->conn, $sql);
        while ($row = mysqli_fetch_assoc($res)) {
            if ($row['haveImages'] == 1) {
                $images = $this->questionImageMappingRepo->getAllByQuestionId($row['questionId']);
            }
            $row['images'] = $images;
            $data[] = $row;
        }

        return $data;
    }

    function softDelete($id, $userId)
    {
        $sql = "UPDATE " . $this->tableName . " set isDeleted = 1, deletedOn = '$this->now', deletedBy = $userId where questionId = '$id'";
        try {
            $res = mysqli_query($this->conn, $sql);
            if ($res) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo sendResponse(false, 500, $e->getMessage());
        }
    }

    function update($model)
    {
        $sql = " 
        update " . $this->tableName . " 
        set question = '$model->question' ,
        option1 = '$model->option1' ,
        option2 = '$model->option2' ,
        option3 = '$model->option3' ,
        option4 = '$model->option4' ,
        correctAns = '$model->correctAns' ,
        marks = '$model->marks' ,
        categoryId = '$model->categoryId' ,
        haveImages = '$model->haveImages' 
        where questionId = '$model->questionId'
        ";

        try {
            $res = mysqli_query($this->conn, $sql);
            if ($res) {
                return true;
            }
        } catch (Exception $e) {
            sendResponse(false, 500, $e->getMessage());
        }

        return false;
    }
}
