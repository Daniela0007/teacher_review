<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="./css/login.css" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <div class="login-container">
      <h1>Evaluare profesori</h1>

      <form id="login-form" class="login-form" action="" method="POST">
        <div class="form-group">
          <label for="email">Mail</label>
          <input
            type="username"
            id="username"
            name="username"
            placeholder="username@info.uaic.ro"
            required
          />
        </div>
        <div class="form-group">
          <label for="password">Parola</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="password"
            required
          />
        </div>
        <button type="submit" name="submit" class="btn" id="login-btn">Login</button>

        <p class="error-message" id="error-message">Nume de utilizator sau parolă invalidă.</p>
        <p class="success-message" id="success-message">Autentificare reușită! Redirecționare în curs...</p>
      </form>
      
    </div>

    <script>
      const usernameInput = document.getElementById("username");
      const passwordInput = document.getElementById("password");
      const loginButton = document.getElementById("login-btn");

      function validateForm() {
        const usernameValue = usernameInput.value;
        const passwordValue = passwordInput.value;

        const emailPattern = /^[a-zA-Z0-9._%+-]+@info\.uaic\.ro$/;

        if (emailPattern.test(usernameValue) && passwordValue.trim() !== "") {
          loginButton.disabled = false;
        } else {
          loginButton.disabled = true;
        }
      }

      document.getElementById('login-form').addEventListener('submit', async function(event) {
          event.preventDefault(); 

          const username = document.getElementById('username').value;
          const password = document.getElementById('password').value;
          const errorMessage = document.getElementById('error-message');
          const successMessage = document.getElementById('success-message');

          errorMessage.style.display = 'none';
          successMessage.style.display = 'none';

          try {
            const response = await fetch('?controller=Login&action=authenticate', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({ username, password }), 
            });

            const result = await response.json();

            if (response.ok && result.status === 'success') {
              successMessage.style.display = 'block';
              successMessage.textContent = 'Autentificare reușită! Redirecționare în curs...';

              setTimeout(() => {
                window.location.href = result.redirect_url;
              }, 2000);
            } else {
              errorMessage.style.display = 'block';
              errorMessage.textContent = result.message || 'Nume de utilizator sau parolă invalidă.';
            }
          } catch (error) {
            errorMessage.style.display = 'block';
            errorMessage.textContent = 'A apărut o eroare neașteptată. Te rugăm să încerci din nou mai târziu.';
            console.error('Login error:', error);
          }
      });

      usernameInput.addEventListener('input', validateForm);
      passwordInput.addEventListener('input', validateForm);
    </script>
  </body>
</html>
