// Simple interaction: highlight selected collection pill
document.addEventListener('DOMContentLoaded', () => {
  console.log('DOM fully loaded and parsed');

  // Filtering logic
  const pills = document.querySelectorAll('.pill');
  const productsGrid = document.getElementById('products-grid');

  if (!productsGrid) {
    console.error('Error: products-grid element not found in DOM');
    return;
  }

  const renderProducts = (productsToRender) => {
    console.log('Rendering products:', productsToRender.length);
    productsGrid.innerHTML = '';

    if (!productsToRender || productsToRender.length === 0) {
      productsGrid.innerHTML = '<p class="no-products">No products found matching this filter.</p>';
      return;
    }

    productsToRender.forEach(product => {
      const article = document.createElement('article');
      article.className = 'card';

      const detailLink = product.id ? `./product_detail.php?id=${product.id}` : '#';
      const content = (product.description || '') + `\n\nPrice: ${product.price || 'N/A'} $`;
      const imgUrl = product.img || 'https://via.placeholder.com/300';

      article.innerHTML = `
        <a href="${detailLink}" style="text-decoration: none; color: inherit;">
          <div class="card__image">
            <img src="${imgUrl}" alt="${product.title}" onerror="this.src='https://via.placeholder.com/300'">
          </div>
          <div class="card__body">
            <h3>${product.title}</h3>
            <p>${content}</p>
            <span class="link">${product.status}</span>
          </div>
        </a>
      `;
      productsGrid.appendChild(article);
    });
  };

  const filterProducts = (filter) => {
    console.log('Filtering for:', filter);

    // Safety check for allProducts
    if (typeof window.allProducts === 'undefined' || !Array.isArray(window.allProducts)) {
      console.error('window.allProducts is missing or invalid:', window.allProducts);
      productsGrid.innerHTML = '<p class="error">Error loading products data.</p>';
      return;
    }

    let filtered = [];
    if (filter === 'all') {
      filtered = window.allProducts;
    } else {
      const filterLower = filter.toLowerCase();
      filtered = window.allProducts.filter(p => {
        // Safe access to properties
        const sex = (p.sex || '').toLowerCase();
        const categories = (p.categories || '').toLowerCase();

        return sex === filterLower || categories.includes(filterLower);
      });
    }

    console.log(`Found ${filtered.length} products for filter '${filter}'`);
    renderProducts(filtered);
  };

  pills.forEach((pill) => {
    pill.addEventListener('click', () => {
      pills.forEach((p) => p.classList.remove('pill--active'));
      pill.classList.add('pill--active');

      const filter = pill.getAttribute('data-filter');
      if (filter) {
        filterProducts(filter);
      }
    });
  });

  // Initial render
  if (typeof window.allProducts !== 'undefined') {
    console.log('Initial render triggered');
    filterProducts('all');
  } else {
    console.warn('window.allProducts not defined on load');
    // Periodically check in case of slow parsing (unlikely but safe)
    setTimeout(() => {
      if (typeof window.allProducts !== 'undefined') {
        console.log('Late render triggered');
        filterProducts('all');
      }
    }, 500);
  }

  // Mobile menu logic (unchanged)
  const burger = document.querySelector('.burger');
  const menu = document.getElementById('mobileMenu');

  const setOpen = (open) => {
    if (!burger || !menu) return;
    if (open) {
      menu.hidden = false;
      burger.setAttribute('aria-expanded', 'true');
    } else {
      menu.hidden = true;
      burger.setAttribute('aria-expanded', 'false');
    }
  };

  if (burger && menu) {
    setOpen(false);
    burger.addEventListener('click', (e) => {
      e.stopPropagation();
      setOpen(menu.hidden);
    });
    menu.addEventListener('click', (e) => {
      const link = e.target && e.target.closest && e.target.closest('a');
      if (link) setOpen(false);
    });
    document.addEventListener('click', (e) => {
      const isInMenu = menu.contains(e.target);
      const isBurger = burger.contains(e.target);
      if (!isInMenu && !isBurger) setOpen(false);
    });
  }
});
