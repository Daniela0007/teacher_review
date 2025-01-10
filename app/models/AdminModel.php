<?php
require_once __DIR__ . '/Database.php';
class AdminModel{
  protected $db;

  public function __construct() {
      $this->db = Database::getConnection();
  }

  public function getTeachersList() {
      $filePath = __DIR__ . "/../utils/professors.json";
      if (!file_exists($filePath)) {
          throw new Exception("File not found: $filePath");
      }

      $jsonContent = file_get_contents($filePath);

      if ($jsonContent === false) {
          throw new Exception("Failed to read file: $filePath");
      }

      $professors = json_decode($jsonContent, true);

      if ($professors === null) {
          throw new Exception("Invalid JSON in file: $filePath");
      }

      return $professors;
  }

  public function getFeedbacks($teacher) {
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
                    'student_mail' => $row['student_mail'],
                    'course' => $row['course'],
                    'year' => $row['year'],
                    'positive_feedback' => $row['positive_feedback'],
                    'negative_feedback' => $row['negative_feedback'],
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
        return ['error' => true, 'message' => $e->getMessage()];
    }
  }

  public function getFeedbacksFilter($year, $course, $professor, $type = null)
  {
    try{
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
            WHERE Feedback.professor = :professor
              AND Feedback.year = :year
              AND Feedback.course = :course
       ";
      if ($type) {
          $sql .= " AND Feedback.type = :type";
      }

      $sql .= " ORDER BY Feedback.id, FeedbackAnswers.question_id";

      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':professor', $professor, PDO::PARAM_STR);
      $stmt->bindParam(':year', $year, PDO::PARAM_STR);
      $stmt->bindParam(':course', $course, PDO::PARAM_STR);

      if ($type) {
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
      }

      $stmt->execute();

      $feedbacks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $feedback_id = $row['feedback_id'];

            if (!isset($feedbacks[$feedback_id])) {
                $feedbacks[$feedback_id] = [
                    'feedback_id' => $row['feedback_id'],
                    'student_mail' => $row['student_mail'],
                    'course' => $row['course'],
                    'type' => $row['type'],
                    'year' => $row['year'],
                    'positive_feedback' => $row['positive_feedback'],
                    'negative_feedback' => $row['negative_feedback'],
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

}