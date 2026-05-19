<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Perfume | About us</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./style.css">
</head>
<body>
  <div class="page">
      <?php include './components/navbar.php'; ?>

    <main id="top">
      <section class="about-hero">
        <div class="about-hero__text">
          <p class="eyebrow">About us</p>
          <h1>Timepieces crafted for every moment</h1>
          <p>
            My Perfume sélectionne des montres qui allient précision et élégance.
            Nous travaillons avec des maisons reconnues pour leurs chronographes
            et leurs finitions soignées, afin que chaque seconde compte.
          </p>
        </div>
        <div class="about-hero__image">
          <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=700&q=80" alt="Montre élégante">
        </div>
      </section>

      <section class="about about--page">
        <div class="about__content">
          <div class="about__text">
            <h2>Notre engagement</h2>
            <p>
              Nous accompagnons collectionneurs et passionnés pour trouver la montre
              qui correspond à leur style de vie : sportif, classique ou élégant.
              Chaque pièce est vérifiée, emballée avec soin et expédiée en toute sécurité.
            </p>
            <p>
              Notre équipe reste disponible après l’achat pour le suivi, l’entretien
              et le conseil, afin que votre montre reste fiable au fil du temps.
            </p>
          </div>
          <div class="about__card">
            <div class="about__badge">Trusted selection</div>
            <p class="about__stat">+1200</p>
            <p class="about__caption">Montres expédiées avec soin</p>
          </div>
        </div>
      </section>
    </main>

    <?php include './components/footer.php'; ?>
  </div>

  <script src="./script.js"></script>
</body>
</html>

