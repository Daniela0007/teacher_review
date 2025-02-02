<?php
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Feedback Page</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="./css/student.css" />
  </head>
  <body>
    <main>
      <!-- Hero Section -->
      <div class="header">
        <img src="./assets/faculty_logo.svg" alt="Faculty Logo" style="height: 50px" />
        <div class="buton">
          <form action="?controller=Login&action=logout" method="POST" style="display: inline;">
              <button type="submit" class="logout-button">
                <i class="fas fa-sign-out-alt"></i> Logout
              </button>
          </form>
        </div>
      </div>

      <section class="hero">
        <div class="hero-content">
          <h1>Părerea ta contează pentru noi!</h1>
          <p>
            Ajută-ne să îmbunătățim experiența educațională prin opiniile tale
            sincere și constructive.
          </p>
        </div>
      </section>

      <!-- Feedback Form Section -->
      <section class="feedback-form">
        <div class="feedback-container">
          <h2 class="mb-4">Lasă-ne recenzia ta!</h2>

          <form action="" method="post" id="feedbackForm">
            <!-- Year Dropdown -->
            <div class="row">
              <label for="year" class="form-label"
                ><i class="fas fa-calendar-alt"></i> Anul de studiu</label
              >
              <select
                id="year"
                name="year"
                class="form-select"
                onchange="updateCourses(this.value)"
              >
                <option value="" disabled selected>Anul de studiu</option>
                <?php
                  ksort($data);
                  
                   foreach ($data as $year => $details) {
                    echo "<option value='" . htmlspecialchars($year) . "'>$year</option>";
                }
                ?>
              </select>
            </div>

            <!-- Courses Dropdown -->
            <div class="row">
              <label for="courses" class="form-label"
                ><i class="fas fa-book"></i> Cursul</label
              >
              <select
                id="courses"
                name="course"
                class="form-select"
                onchange="updateProfessors(this.value)"
                disabled
              >
                <option value="" disabled selected>Curs</option>
              </select>
            </div>

            <!-- Professors Dropdown -->
            <div class="row">
              <label for="professors" class="form-label"
                ><i class="fas fa-chalkboard-teacher"></i> Profesorul</label
              >
              <select
                id="professors"
                name="professor"
                class="form-select"
                disabled
              >
                <option value="" disabled selected>Profesor</option>
              </select>
            </div>

            <!-- Type Dropdown -->
            <div class="row">
              <label for="type" class="form-label">
                <i class="fas fa-list-alt"></i> Formatul activității
              </label>
              <select id="type" name="type" class="form-select" required>
                <option value="" disabled selected>Selectează tipul</option>
                <option value="curs">Curs</option>
                <option value="seminar">Seminar</option>
              </select>
            </div>

            <!-- Questions Container -->
            <div class="row" id="questions-container">
            </div>

            <!-- Feedback Text: Good Aspects -->
            <div class="row">
              <label for="positive-feedback" class="form-label">
                <i class="fas fa-thumbs-up"></i> Aspecte pozitive
              </label>
              <textarea
                id="positive-feedback"
                name="positive_feedback"
                class="form-control"
                rows="4"
                placeholder="Scrie aici aspectele pozitive..."
              ></textarea>
            </div>

            <!-- Feedback Text: Negative Aspects -->
            <div class="row">
              <label for="negative-feedback" class="form-label">
                <i class="fas fa-thumbs-down"></i> Aspecte negative
              </label>
              <textarea
                id="negative-feedback"
                name="negative_feedback"
                class="form-control"
                rows="4"
                placeholder="Scrie aici aspectele negative..."
              ></textarea>
            </div>

            <!-- Submit Button -->
            <div class="row">
              <button
                class="btn btn-primary w-100"
                id="submitEvaluation"
                type="submit"
                disabled
              >
                <i class="fas fa-paper-plane"></i> Trimite recenzia
              </button>
            </div>
            
          </form>
        </div>
      </section>

      <!-- Reviews Section -->
      <div class="my-reviews"  id="my-reviews">
         <!-- <h2>Răsfoiește recenziile tale anterioare</h2>
        <div class="review-container" id="review-container">
        </div> -->

        <div class="reviews-container">
          <h3>Recenziile Mele</h3>
          <div class="table-container">
              <table>
                  <thead>
                      <tr>
                          <th>Profesor</th>
                          <th>Materie</th>
                          <th>Tip</th>
                      </tr>
                  </thead>
                  <tbody id="reviews-body">
                      <tr>
                          <td>Prof. Andrei Popescu</td>
                          <td>Algoritmi și Structuri de Date</td>
                          <td>Curs</td>
                      </tr>
                      <tr>
                          <td>Prof. Ioana Vasilescu</td>
                          <td>Programare Web</td>
                          <td>Seminar</td>
                      </tr>
                      <tr>
                          <td>Prof. Mihai Radu</td>
                          <td>Inteligență Artificială</td>
                          <td>Curs</td>
                      </tr>
                  </tbody>
              </table>
          </div>
        </div>
      </div>

    </main>
    
    <script>
        const data = <?php echo json_encode($data); ?>;
        const form = document.getElementById('feedbackForm');
        const submitButton = document.getElementById('submitEvaluation');
        const questionToCategoryMap = {};
        let formQuestions = [];

        function fetchJsonData() {
              return fetch('../app/utils/questions.json')
                  .then((response) => {
                      if (!response.ok) {
                          throw new Error('Failed to fetch JSON data');
                      }
                      return response.json();
                  })
                  .then((data) => {
                      data.categories.forEach(category => {
                          category.questions.forEach(question => {
                              questionToCategoryMap[question] = category.category_name;
                          });
                      });
                      console.log('Question to Category Map:', questionToCategoryMap); // Debugging
                  })
                  .catch((error) => {
                      console.error('Error fetching JSON:', error);
                      throw error;
                  });
          }

        form.addEventListener('input', () => {
            const year = document.getElementById('year').value;
            const course = document.getElementById('courses').value;
            const professor = document.getElementById('professors').value;
            const typeActivity = document.getElementById('type').value;
            const positiveFeedback = document.querySelector('[name="positive_feedback"]').value;
            const negativeFeedback = document.querySelector('[name="negative_feedback"]').value;

            const questions = document.querySelectorAll(".radios");
            let allAnswered = true;

            questions.forEach((radios) => {
                const selected = radios.querySelector("input[type='radio']:checked");
                if (!selected) {
                    allAnswered = false; 
                }
            });

            if (year && course && professor && positiveFeedback.trim() 
                && negativeFeedback.trim() && allAnswered && typeActivity) {
              submitButton.disabled = false;
            } else {
              submitButton.disabled = true;
            }
        });

        function updateCourses(year) {
            const coursesDropdown = document.getElementById("courses");
            const professorsDropdown = document.getElementById("professors");
            submitButton.disabled = true;

            coursesDropdown.innerHTML = '<option value="" disabled selected>Selectează cursul</option>';
            professorsDropdown.innerHTML = '<option value="" disabled selected>Selectează profesorul</option>';
            professorsDropdown.disabled = true;

            if (data[year] && data[year].courses) {
                for (const course in data[year].courses) {
                    const option = document.createElement("option");
                    option.value = course;
                    option.textContent = course;
                    coursesDropdown.appendChild(option);
                }
                coursesDropdown.disabled = false;
            }
        }

        function updateProfessors(course) {
            const year = document.getElementById("year").value;
            const professorsDropdown = document.getElementById("professors");
            submitButton.disabled = true;

            professorsDropdown.innerHTML = '<option value="" disabled selected>Selectează profesorul</option>';

            if (data[year] && data[year].courses && data[year].courses[course]) {
                data[year].courses[course].forEach(professor => {
                    const option = document.createElement("option");
                    option.value = professor;
                    option.textContent = professor;
                    professorsDropdown.appendChild(option);
                });
                professorsDropdown.disabled = false;
            }
        }

        function displayMessage(message, type) {
            const messageContainer = document.createElement('div');
      
            messageContainer.className = `${type}-message`;
            messageContainer.textContent = message;

            document.body.appendChild(messageContainer);

            setTimeout(() => {
                messageContainer.remove();
            }, 3000);
        }

        document.getElementById('feedbackForm').addEventListener('submit', function (e) {
            e.preventDefault(); 

            const negativeFeedback = document.getElementById('negative-feedback').value;
            const positiveFeedback = document.getElementById('positive-feedback').value;

            const xssPattern = /<[^>]*>|<script.*?>.*?<\/script.*?>|javascript:|on\w+\s*=/gi;

            if (xssPattern.test(negativeFeedback) || xssPattern.test(positiveFeedback)) {
                displayMessage("Input invalid în feedback.", "error");
                return; 
            }

            const formData = new FormData(this); 
            const payload = {};

            formData.forEach((value, key) => {
                if (!key.startsWith('question')) {
                    payload[key] = value;
                }
            });

            const selectedAnswers = [];
            const selectedAnswersText = [];
            const radios = this.querySelectorAll('input[type="radio"]:checked');
            radios.forEach((radio) => {
              const label = radio.parentNode;
              const text = label ? label.textContent.trim() : null;

              const questionId = parseInt(radio.getAttribute('data-question-id'));
              const questionText = formQuestions.find(q => q.id == questionId)?.question_text || "Unknown question";
              console.log('Question text:', questionText);

              selectedAnswers.push({
                  question_id: questionId,
                  question_text: questionText,
                  answer_text: text
              });

            });

            payload['feedback_answers'] = selectedAnswers;

            console.log('Payload:', payload);

            fetch('?controller=Student&action=save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload), 
            })
            .then((response) => {
                    return response.json().then((data) => {
                        if (!response.ok) {
                            //console.log("Error: ", data);
                            throw new Error(data.error || "An unknown error occurred.");
                        }
                        return data; 
                    });
             })
                .then((data) => {
                    //console.log('Server response data:', data);
                    
                    displayMessage(data.message, 'success');

                    const tableBody = document.getElementById('reviews-body');

                    const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${payload.professor}</td>
                            <td>${payload.course}</td>
                            <td>${payload.type.charAt(0).toUpperCase() + payload.type.slice(1)}</td>
                        `;
                    tableBody.appendChild(row);

                    // const reviewContainer = document.getElementById("review-container");
                    // const reviewSection = document.getElementById("my-reviews");
                    
                    // if (reviewSection.style.display === 'none') {
                    //   reviewSection.style.display = '';
                    // }

                    // const groupedAnswers = {};
                    // payload.feedback_answers.forEach(answer => {
                    //         const categoryName = questionToCategoryMap[answer.question_text];
                    //         if (!groupedAnswers[categoryName]) {
                    //             groupedAnswers[categoryName] = [];
                    //         }
                    //         groupedAnswers[categoryName].push(answer);
                    // });

                    // const feedbackCategoriesHTML = Object.keys(groupedAnswers)
                    //         .map(categoryName => {
                    //             const answers = groupedAnswers[categoryName];

                    //             const colorRow = answers
                    //                 .map(answer => {
                    //                     let color;
                    //                     switch (answer.answer_text) {
                    //                       case "Întotdeauna":
                    //                             color = "#8BC34A"; 
                    //                             break;
                    //                         case "Destul de des":
                    //                             color = "#CDDC39"; 
                    //                             break;
                    //                         case "Destul de rar":
                    //                             color = "#FFC107"; 
                    //                             break;
                    //                         case "Niciodată":
                    //                             color = "#FF5722"; 
                    //                             break;
                    //                         case "Nu mă pot pronunța":
                    //                             color = "#9E9E9E"; 
                    //                             break;
                    //                     }

                    //                     return `<div class="color-box" 
                    //                                   style="background-color: ${color}"
                    //                                   title="${answer.answer_text}"
                    //                                   data-question="${answer.question_text}"></div>`;
                    //                  })
                    //                 .join("");

                    //             return `
                    //                 <div class="feedback-category">
                    //                     <div class="color-row">${colorRow}</div>
                    //                     <div class="hover-question" style="margin-top: 10px; font-size: 12px; color: #555; display: none;"></div>
                    //                 </div>
                    //             `;
                    //         })
                    //         .join("");

                    // //console.log('questionMap:', questionMap); 

                    // const reviewCard = `
                    //      <div class="review-card">
                    //       <div class="card-body">

                    //             <div class="card-header">
                    //               <h5 class="card-title">
                    //                 <i class="fas fa-user-tie"></i> ${payload.professor}
                    //               </h5>

                    //               <div class="legend">
                    //                   <i class="fas fa-info-circle legend-icon" title="Vezi legenda"></i>
                    //                   <div class="legend-box">
                    //                       <div class="legend-item">
                    //                           <span class="color-box" style="background-color: #8BC34A;"></span>
                    //                           Întotdeauna
                    //                       </div>
                    //                       <div class="legend-item">
                    //                           <span class="color-box" style="background-color: #CDDC39;"></span>
                    //                           Destul de des
                    //                       </div>
                    //                       <div class="legend-item">
                    //                           <span class="color-box" style="background-color: #FFC107;"></span>
                    //                           Destul de rar
                    //                       </div>
                    //                       <div class="legend-item">
                    //                           <span class="color-box" style="background-color: #FF5722;"></span>
                    //                           Niciodată
                    //                       </div>
                    //                       <div class="legend-item">
                    //                           <span class="color-box" style="background-color: #9E9E9E;"></span>
                    //                           Nu mă pot pronunța
                    //                       </div>
                    //                   </div>
                                      
                    //               </div>
                    //             </div>

                    //         <h6 class="card-subtitle">
                    //           <i class="fas fa-book"></i> ${payload.course} 
                    //           <span class="badge">${payload.type === 'curs' ? 'Curs' : 'Seminar'}</span>
                    //         </h6>
                    //         <p class="card-text">
                    //           <i class="fas fa-thumbs-up"></i> ${payload.positive_feedback}
                    //         </p>
                    //         <p class="card-text">
                    //           <i class="fas fa-thumbs-down"></i> ${payload.negative_feedback}
                    //         </p>
                    //         <div class="feedback-categories">
                    //               ${feedbackCategoriesHTML}  
                    //         </div>
                    //         <div class="clicked-question-container"></div>
                    //       </div>
                    //     </div>
                    // `;

                    // reviewContainer.innerHTML += reviewCard;

                    // // const newReviewCard = createReviewCard(payload); 
                    // // reviewContainer.innerHTML += newReviewCard;

                    // document.querySelectorAll('.legend').forEach(legend => {
                    //     const legendBox = legend.querySelector('.legend-box');

                    //     legend.querySelector('.legend-icon').addEventListener('click', () => {
                    //         if (legendBox.style.display === 'block') {
                    //             legendBox.style.display = 'none';
                    //         } else {
                    //             legendBox.style.display = 'block';
                    //         }
                    //     });

                    //     document.addEventListener('click', (e) => {
                    //         if (!legend.contains(e.target)) {
                    //             legendBox.style.display = 'none';
                    //         }
                    //     });
                    // });

                    // document.querySelectorAll('.review-card').forEach(card => {
                    //     const colorBoxes = card.querySelectorAll('.color-box');
                    //     const questionContainer = card.querySelector('.clicked-question-container');

                    //     colorBoxes.forEach(colorBox => {
                    //         const questionText = colorBox.getAttribute('data-question');

                    //         colorBox.addEventListener('click', () => {
                    //             if (questionContainer.textContent === questionText) {
                    //                 questionContainer.textContent = ''; 
                    //             } else {
                    //                 questionContainer.textContent = questionText; 
                    //             }
                    //         });
                    //     });
                    // });

                    this.reset();
                    const submitButton = document.getElementById('submitEvaluation');
                    submitButton.disabled = true;

                    document.getElementById("courses").disabled = true;
                    document.getElementById("professors").disabled = true;
                })
                .catch((error) => {
                  displayMessage(error.message || "Ceva nu a mers bine. Încearcă din nou mai târziu.", 'error');
                });
        });

        function getLastReviews() {
          fetch('?controller=Student&action=getLastReviews', {
                method: 'GET',
            })
                .then(response => {
                  if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                  }
                  return response.json(); 
                })
                .then(data => {
                    console.log('data: ', data);

                    const tableBody = document.getElementById('reviews-body');
                    tableBody.innerHTML = ''; 

                    if (!data.feedbacks || data.feedbacks.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="3" class="no-reviews">Nu ai oferit încă nicio recenzie.</td></tr>';
                        return;
                    }

                    data.feedbacks.forEach(review => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${review.professor}</td>
                            <td>${review.course}</td>
                            <td>${review.type.charAt(0).toUpperCase() + review.type.slice(1)}</td>
                        `;
                        tableBody.appendChild(row);
                    });
                    
                }).catch(error => {
                  console.error('Fetch error:', error);
                  displayMessage('Ceva nu a mers bine.Te rugăm să incerci mai târziu.', 'error');
                });

        }

        function loadQuestions() {
            fetch('?controller=Student&action=getQuestions', {
                method: 'GET',
            })
                .then(response => 
                  // console.log("These are the form Questions: ", response.json());
                  response.json()
                )
                .then(data => {
                    formQuestions = data.questions;
                    console.log("These are the form Questions: ", formQuestions);
                    const questions = data.questions;
                    const container = document.getElementById('questions-container');
                    container.innerHTML = '';

                    const answers = [
                        { value: '1', text: 'Niciodată' },
                        { value: '2', text: 'Destul de rar' },
                        { value: '3', text: 'Destul de des' },
                        { value: '4', text: 'Întotdeauna' },
                        { value: '0', text: 'Nu mă pot pronunța' }
                    ];

                    questions.forEach((question) => {
                        const questionDiv = document.createElement("div");
                        questionDiv.className = "row";

                        let radiosHTML = '';
                        answers.forEach(answer => {
                            radiosHTML += `
                                <label class="radio-inline">
                                    <input type="radio" name="question${question.id}" value="${answer.value}" data-question-id="${question.id}" 
                                        ${answer.value === '0' ? 'checked' : ''} />
                                    ${answer.text}
                                </label>
                            `;
                        });

                        questionDiv.innerHTML = `
                            <label class="form-label">
                                <i class="fas fa-question-circle"></i> ${question.question_text}
                            </label>
                            <div class="radios">
                                ${radiosHTML}
                            </div>
                        `;

                        container.appendChild(questionDiv);
                    });
                })
                .catch(error => console.error('Error loading questions:', error));
        }

        // function getFeedbacks() {
        //  //fetchJsonData();

        //     fetch('?controller=Student&action=getFeedbacks', {
        //         method: 'GET',
        //     })
        //         .then(response => {
        //           if (!response.ok) {
        //             throw new Error(`HTTP error! Status: ${response.status}`);
        //           }
        //           return response.json(); 
        //         })
        //         .then(data => {
        //             //console.log('Feedbacks:', data);

        //             if (data.feedbacks.length === 0) {
        //                 //console.log('No feedbacks available.');
        //                 //displayMessage('No feedbacks found.', 'info');
        //                 const reviewContainer = document.getElementById("my-reviews");
        //                 reviewContainer.style.display = '';
        //             } else {
        //                 const feedbacks = data.feedbacks;
        //                 const myReviewsContainer = document.getElementById("my-reviews");
        //                 const reviewContainer = document.getElementById("review-container");

        //                 if (myReviewsContainer.style.display === 'none') {
        //                     myReviewsContainer.style.display = '';
        //                 }

        //                 reviewContainer.innerHTML = '';

        //                 feedbacks.forEach(feedback => {
        //                   const groupedAnswers = {};
        //                   feedback.feedback_answers.forEach(answer => {
        //                     const categoryName = questionToCategoryMap[answer.question];
        //                     if (!groupedAnswers[categoryName]) {
        //                         groupedAnswers[categoryName] = [];
        //                     }
        //                     groupedAnswers[categoryName].push(answer);
        //                 });

        //                 const feedbackCategoriesHTML = Object.keys(groupedAnswers)
        //                     .map(categoryName => {
        //                         const answers = groupedAnswers[categoryName];

        //                         const colorRow = answers
        //                             .map(answer => {
        //                                 let color;
        //                                 switch (answer.answer) {
        //                                   case "Întotdeauna":
        //                                         color = "#8BC34A"; 
        //                                         break;
        //                                     case "Destul de des":
        //                                         color = "#CDDC39"; 
        //                                         break;
        //                                     case "Destul de rar":
        //                                         color = "#FFC107"; 
        //                                         break;
        //                                     case "Niciodată":
        //                                         color = "#FF5722"; 
        //                                         break;
        //                                     case "Nu mă pot pronunța":
        //                                         color = "#9E9E9E"; 
        //                                         break;
        //                                 }

        //                                 return `<div class="color-box" 
        //                                               style="background-color: ${color}"
        //                                               title="${answer.answer}"
        //                                               data-question="${answer.question}"></div>`;
        //                             })
        //                             .join("");

        //                         return `
        //                             <div class="feedback-category">
        //                                 <div class="color-row">${colorRow}</div>
        //                                 <div class="hover-question" style="margin-top: 10px; font-size: 12px; color: #555; display: none;"></div>
        //                             </div>
        //                         `;
        //                     })
        //                     .join("");
                          
        //                 const reviewCard = `
        //                       <div class="review-card">

        //                         <div class="card-body">

        //                         <div class="card-header">
        //                           <h5 class="card-title">
        //                             <i class="fas fa-user-tie"></i> ${feedback.professor}
        //                           </h5>

        //                           <div class="legend">
        //                               <i class="fas fa-info-circle legend-icon" title="Vezi legenda"></i>
        //                               <div class="legend-box">
        //                                   <div class="legend-item">
        //                                       <span class="color-box" style="background-color: #8BC34A;"></span>
        //                                       Întotdeauna
        //                                   </div>
        //                                   <div class="legend-item">
        //                                       <span class="color-box" style="background-color: #CDDC39;"></span>
        //                                       Destul de des
        //                                   </div>
        //                                   <div class="legend-item">
        //                                       <span class="color-box" style="background-color: #FFC107;"></span>
        //                                       Destul de rar
        //                                   </div>
        //                                   <div class="legend-item">
        //                                       <span class="color-box" style="background-color: #FF5722;"></span>
        //                                       Niciodată
        //                                   </div>
        //                                   <div class="legend-item">
        //                                       <span class="color-box" style="background-color: #9E9E9E;"></span>
        //                                       Nu mă pot pronunța
        //                                   </div>
        //                               </div>
                                      
        //                           </div>
        //                         </div>

        //                           <h6 class="card-subtitle">
        //                             <i class="fas fa-book"></i> ${feedback.course} 
        //                             <span class="badge">${feedback.type === 'curs' ? 'Curs' : 'Seminar'}</span>
        //                           </h6>
        //                           <p class="card-text">
        //                             <i class="fas fa-thumbs-up"></i> ${feedback.positive_feedback}
        //                           </p>
        //                           <p class="card-text">
        //                             <i class="fas fa-thumbs-down"></i> ${feedback.negative_feedback}
        //                           </p>
        //                           <div class="feedback-categories">
        //                              ${feedbackCategoriesHTML}  
        //                           </div>
        //                           <div class="clicked-question-container"></div>
        //                       </div>
        //                     </div>
        //                   `;

        //                 reviewContainer.innerHTML += reviewCard;
        //               });
        //             }

        //             document.querySelectorAll('.legend').forEach(legend => {
        //                 const legendBox = legend.querySelector('.legend-box');

        //                 legend.querySelector('.legend-icon').addEventListener('click', () => {
        //                     if (legendBox.style.display === 'block') {
        //                         legendBox.style.display = 'none';
        //                     } else {
        //                         legendBox.style.display = 'block';
        //                     }
        //                 });

        //                 document.addEventListener('click', (e) => {
        //                     if (!legend.contains(e.target)) {
        //                         legendBox.style.display = 'none';
        //                     }
        //                 });
        //             });

        //             document.querySelectorAll('.review-card').forEach(card => {
        //                 const colorBoxes = card.querySelectorAll('.color-box');
        //                 const questionContainer = card.querySelector('.clicked-question-container');

        //                 colorBoxes.forEach(colorBox => {
        //                     const questionText = colorBox.getAttribute('data-question');

        //                     colorBox.addEventListener('click', () => {
        //                         if (questionContainer.textContent === questionText) {
        //                             questionContainer.textContent = ''; 
        //                         } else {
        //                             questionContainer.textContent = questionText; 
        //                         }
        //                     });
        //                 });
        //             });

        //         }).catch(error => {
        //               console.error('Fetch error:', error);
        //               displayMessage('Ceva nu a mers bine.Te rugăm să incerci mai târziu.', 'error');
        //         });
        // }
              
        document.addEventListener('DOMContentLoaded', () => {
            loadQuestions();
            fetchJsonData()
                .then(() => {
                    // studentul nu isi poate vedea propriile feebackuri deocamdata
                    // getFeedbacks();
                   getLastReviews();
                });
        });

        
    </script>
  </body>
</html>