DROP TABLE IF EXISTS Questions;

DROP TABLE IF EXISTS Feedback;

DROP TABLE IF EXISTS FeedbackAnswers;


CREATE TABLE Questions
(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  question_text TEXT NOT NULL
);

CREATE TABLE Feedback
(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
    course VARCHAR
(255) NOT NULL,
  student_mail VARCHAR
(255) NOT NULL,
    professor VARCHAR
(255) NOT NULL,
    year VARCHAR
(255) NOT NULL,
    positive_feedback TEXT,
    negative_feedback TEXT,
    type VARCHAR
(50) NOT NULL DEFAULT 'curs' CHECK
(type IN
('curs', 'seminar'))
);

CREATE TABLE FeedbackAnswers
  (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    feedback_id INT NOT NULL,
    question_id INT NOT NULL,
    answer INT NOT NULL,
    FOREIGN KEY
(feedback_id) REFERENCES Feedback
(id) ON
DELETE CASCADE,
    FOREIGN KEY (question_id)
REFERENCES Questions
(id) ON
DELETE CASCADE
);
