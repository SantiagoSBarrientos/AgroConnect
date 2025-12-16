<?php
header("Content-Type: text/css; charset=UTF-8");

$page = isset($_GET['page']) ? $_GET['page'] : 'default';


$primary = "#228b22";
$background = "#f4f6f9";
?>

<style>
body {
  background: <?= $background ?>;
  color: #333;
  margin: 0;
  font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Helvetica Neue", Arial, sans-serif;
  transition: background 0.5s ease;
}

header {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  padding: 25px 60px;
  background: rgba(255,255,255,0.7);
  backdrop-filter: blur(12px);
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  position: sticky;
  top: 0;
  z-index: 10;
  animation: fadeInDown 0.8s ease;
}

header h1 {
  font-weight: 800;
  font-size: 2.8rem;
  text-align: center;
  color: <?= $primary ?>;
  text-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
  letter-spacing: 1px;
  margin-bottom: 10px;
  animation: fadeZoom 1.2s ease;
}


nav {
  margin-top: 10px;
}

nav a {
  color: <?= $primary ?>;
  text-decoration: none;
  margin: 0 15px;
  font-weight: 600;
  transition: color 0.3s, transform 0.2s;
}

nav a:hover {
  color: #007aff;
  transform: translateY(-2px);
}


main {
  max-width: 1000px;
  margin: 80px auto;
  text-align: center;
  animation: fadeInUp 1.2s ease;
}

h2 {
  font-size: 2rem;
  margin-bottom: 15px;
  font-weight: 700;
}

p {
  font-size: 1.1rem;
  line-height: 1.6;
  color: #444;
  margin-bottom: 40px;
}


.btn {
  background: <?= $primary ?>;
  color: white;
  padding: 12px 28px;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 500;
  margin: 0 10px;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 6px 15px rgba(0,0,0,0.15);
  display: inline-block;
}

.btn:hover {
  transform: translateY(-3px);
  background: linear-gradient(90deg, <?= $primary ?>, #007aff);
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

/* ====== FOOTER ====== */
footer {
  text-align: center;
  padding: 25px 0;
  color: #777;
  border-top: 1px solid rgba(0,0,0,0.05);
  background: rgba(255,255,255,0.5);
  backdrop-filter: blur(10px);
  animation: fadeInUp 1.5s ease;
  position: relative;
  bottom: 0;
  width: 100%;
}

/* ====== ANIMACIONES ====== */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeInDown {
  from { opacity: 0; transform: translateY(-30px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeZoom {
  from { opacity: 0; transform: scale(0.8); }
  to { opacity: 1; transform: scale(1); }
}
</style>
