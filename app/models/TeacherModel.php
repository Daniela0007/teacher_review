<?php
require_once __DIR__ . '/Database.php';

class TeacherModel {
  private $db;

  public function __construct() {
    $this->db = Database::getConnection();
  }

  public function getFeedbacks($teacher) {
    try {
        $sql = "
            SELECT 
                Feedback.id AS feedback_id,
                Feedback.course,
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
            WHERE Feedback.professor = :professor
            ORDER BY Feedback.id, FeedbackAnswers.question_id;
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':professor', $teacher);
        $stmt->execute();

        $feedbacks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $feedback_id = $row['feedback_id'];

            if (!isset($feedbacks[$feedback_id])) {
                $feedbacks[$feedback_id] = [
                    'feedback_id' => $row['feedback_id'],
                    'course' => $row['course'],
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

  public function getFeedbacksFilter($teacher, $type, $subject) {
    try {
        $sql = "
            SELECT 
                Feedback.id AS feedback_id,
                Feedback.course,
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
            WHERE Feedback.professor = :professor
              AND (:type IS NULL OR Feedback.type = :type)
              AND (:subject IS NULL OR Feedback.course = :subject)
            ORDER BY Feedback.id, FeedbackAnswers.question_id;
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':professor', $teacher);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':subject', $subject);
        $stmt->execute();

        $feedbacks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $feedback_id = $row['feedback_id'];

            if (!isset($feedbacks[$feedback_id])) {
                $feedbacks[$feedback_id] = [
                    'feedback_id' => $row['feedback_id'],
                    'course' => $row['course'],
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

        return array_values($feedbacks ?: []);
      } catch (PDOException $e) {
        return ['error' => true, 'message' => 'Database error: ' . $e->getMessage()];
      } catch (Exception $e) {
        return ['error' => true, 'message' => 'An unexpected error occurred: ' . $e->getMessage()];
    }
  }

  public function getSubjects($teacher) {
    try {
        $sql = "
            SELECT DISTINCT course
            FROM Feedback
            WHERE professor = :professor;
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':professor', $teacher);
        $stmt->execute();

        $subjects = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $subjects[] = $row['course'];
        }

        return $subjects;
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
  }
  
}