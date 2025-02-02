DROP TABLE IF EXISTS Feedback;
DROP TABLE IF EXISTS StudentCourses;
DROP TABLE IF EXISTS Questions;
DROP TABLE IF EXISTS FeedbackAnswers;

-- Create Feedback table
CREATE TABLE Feedback (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    course TEXT NOT NULL,
    student_mail NULL,
    professor TEXT NOT NULL,
    year TEXT NOT NULL,
    positive_feedback TEXT,
    negative_feedback TEXT,
    type TEXT NOT NULL CHECK
(type IN
('curs', 'seminar'))
);

-- Create StudentCourses table
CREATE TABLE StudentCourses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_mail TEXT NOT NULL,
    course TEXT NOT NULL,
    professor TEXT NOT NULL,
    type TEXT NOT NULL CHECK
(type IN
('curs', 'seminar'))
);

-- Create Questions table
CREATE TABLE Questions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    question_text TEXT NOT NULL
);

-- Create FeedbackAnswers table (assuming it connects Feedback and Questions)
CREATE TABLE FeedbackAnswers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    feedback_id INTEGER NOT NULL,
    question_id INTEGER NOT NULL,
    answer TEXT NOT NULL,
    FOREIGN KEY
(feedback_id) REFERENCES Feedback
(id) ON
DELETE CASCADE,
    FOREIGN KEY (question_id)
REFERENCES Questions
(id) ON
DELETE CASCADE
);