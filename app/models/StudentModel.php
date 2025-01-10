<?php
require_once __DIR__ . '/Database.php';
class StudentModel {
    protected $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function saveFeedback($data, $student_mail) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("INSERT INTO Feedback (student_mail, course, professor, year, positive_feedback, negative_feedback, type) VALUES (:student_mail, :course, :professor, :year, :positive_feedback, :negative_feedback, :type)");

            $stmt->bindParam(':course', $data['course']);
            $stmt->bindParam(':professor', $data['professor']);
            $stmt->bindParam(':year', $data['year']);
            $stmt->bindParam(':positive_feedback', $data['positive_feedback']);
            $stmt->bindParam(':negative_feedback', $data['negative_feedback']);
            $stmt->bindParam(':type', $data['type']);
            $stmt->bindParam(':student_mail',$student_mail);
            $stmt->execute();

            $feedback_id = $this->db->lastInsertId();

            $stmt = $this->db->prepare("INSERT INTO FeedbackAnswers (feedback_id, question_id, answer)
                                          VALUES (:feedback_id, :question_id, :answer)");

            foreach ($data['feedback_answers'] as $answer) {
                $stmt->bindParam(':feedback_id', $feedback_id);
                $stmt->bindParam(':question_id', $answer['question_id']);
                $stmt->bindParam(':answer', $answer['answer']);
                $stmt->execute();
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to insert feedback: " . $e->getMessage());
        }
    }

    public function getAllQuestions() {
        $query = "SELECT * FROM Questions";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFeedbacks($student_mail) {
        try {
            $sql = "
                SELECT 
                    Feedback.id AS feedback_id,
                    Feedback.course,
                    Feedback.student_mail,
                    Feedback.professor,
                    Feedback.year,
                    Feedback.positive_feedback,
                    Feedback.negative_feedback,
                    Feedback.type,
                    Questions.question_text,
                    FeedbackAnswers.answer
                FROM Feedback
                INNER JOIN FeedbackAnswers ON Feedback.id = FeedbackAnswers.feedback_id
                INNER JOIN Questions ON FeedbackAnswers.question_id = Questions.id
                WHERE Feedback.student_mail = :student_mail
                ORDER BY Feedback.id, FeedbackAnswers.question_id;
            ";
    
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':student_mail', $student_mail);
            $stmt->execute();
    
            $feedbacks = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $feedback_id = $row['feedback_id'];
    
                if (!isset($feedbacks[$feedback_id])) {
                    $feedbacks[$feedback_id] = [
                        'feedback_id' => $row['feedback_id'],
                        'course' => $row['course'],
                        'student_mail' => $row['student_mail'],
                        'professor' => $row['professor'],
                        'year' => $row['year'],
                        'positive_feedback' => $row['positive_feedback'],
                        'negative_feedback' => $row['negative_feedback'],
                        'type' => $row['type'],
                        'feedback_answers' => []
                    ];
                }
    
                $feedbacks[$feedback_id]['feedback_answers'][] = [
                    'question' => $row['question_text'],
                    'answer' => $row['answer']
                ];
            }
    
            return array_values($feedbacks);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function reviewExists($student_mail, $professor, $course, $type) {
      $sql = "SELECT COUNT(*) as count FROM Feedback WHERE student_mail = :student_mail AND professor = :professor AND course = :course AND type = :type";
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':student_mail', $student_mail);
      $stmt->bindParam(':professor', $professor);
      $stmt->bindParam(':course', $course);
      $stmt->bindParam(':type', $type);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result['count'] > 0;
    }
}


