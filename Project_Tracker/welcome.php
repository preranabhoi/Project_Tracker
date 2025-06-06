<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to Project Tracker</title>
  <style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  background: linear-gradient(to right, purple, white);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  text-align: center;
  flex-direction: column;
}

.welcome-container h1 {
  font-size: 3rem;
  color: black;
  margin-bottom: 10px;
}

.welcome-container p {
  font-size: 1.2rem;
  color: #444;
  margin-bottom: 40px;
}

/* Arrow Wrapper */
.arrow-wrapper {
  width: 100%;
  overflow: hidden;
  cursor: pointer;
}

/* Arrow Animation */
.arrow {
  font-size: 2.5rem;
  color: purple;
  animation: moveArrow 1.5s infinite;
  display: inline-block;
  padding-left: 20px;
}

@keyframes moveArrow {
  0% {
    transform: translateX(-100%);
    opacity: 0;
  }
  50% {
    transform: translateX(0);
    opacity: 1;
  }
  100% {
    transform: translateX(100%);
    opacity: 0;
  }
}


  </style>
</head>
<body>
  <div class="welcome-container">
    <h1>Welcome to Project Tracker</h1>
    

    <div class="arrow-wrapper" onclick="window.location.href='auth.php'">
      <div class="arrow">➔</div>
    </div>
  </div>
</body>
</html>