// ====================================
// SHOP JAVASCRIPT - Molino del Pallone
// ====================================

// 1. IL NOSTRO DATABASE PRODOTTI
// Questo è un "Array" (una lista) di "Oggetti". Ogni oggetto rappresenta un prodotto
// con tutte le sue caratteristiche (nome, prezzo, descrizione, ecc.)
const products = [
    {
        id: 1, // Un numero unico per identificare il prodotto
        name: "T-shirt Molino Beach",
        category: "abbigliamento",
        price: 12.50,
        emoji: "👕",
        description: "T-shirt in cotone 100% con il logo del Molino del Pallone. Comoda e perfetta per l'estate.",
        badge: "Best" // Questa proprietà è opzionale, serve per mostrare un'etichetta speciale
    },
    {
        id: 2,
        name: "Cartoline Molino del Pallone",
        category: "gadget",
        price: 8.00,
        emoji: "📬",
        description: "Fotografie autentiche del Molino del Pallone, perfette per regali o per decorare la tua casa.",
        badge: null // 'null' significa che questo prodotto non ha etichette
    },
    {
        id: 3,
        name: "Capello MOLINO DEL PALLONE",
        category: "abbigliamento",
        price: 6.00,
        emoji: "🧢",
        description: "Capello in cotone con visiera, con il logo del Molino del Pallone ricamato. Un must per i fan del molino!",
        badge: "New"
    },
    {
        id: 4,
        name: "Borraccia Molino del Pallone",
        category: "gadget",
        price: 10.00,
        emoji: "🚰",
        description: "Borraccia in acciaio inox con il logo del Molino del Pallone. Perfetta per tenere l'acqua fresca durante le attività all'aperto.",
        badge: "Best"
    }

];

// 2. IL CARRELLO
// Questa variabile parte come una lista vuota []. La riempiremo man mano che l'utente compra.
let cart = [];

// ====================================
// AVVIO DEL SITO (INIZIALIZZAZIONE)
// ====================================

// Quando la pagina web ha finito di caricarsi completamente ('DOMContentLoaded')...
document.addEventListener('DOMContentLoaded', function() {
    loadProducts(); // ...mostra i prodotti sullo schermo
    loadCart();     // ...recupera il carrello salvato in memoria (se c'è)
    updateCartUI(); // ...aggiorna i numerini e i testi del carrello
});

// ====================================
// FUNZIONI PER MOSTRARE I PRODOTTI
// ====================================

// Questa funzione prende i prodotti dalla nostra lista e li "disegna" nella pagina
function loadProducts() {
    // Troviamo il contenitore vuoto nel file HTML
    const container = document.getElementById('products-container');
    
    // Creiamo una variabile di testo vuota dove scriveremo il nostro HTML
    let htmlContent = '';

    // Passiamo in rassegna (forEach) ogni singolo prodotto della nostra lista
    products.forEach(function(product) {
        
        // Prepariamo l'etichetta speciale (se esiste)
        let badgeHtml = '';
        if (product.badge) {
            badgeHtml = `<div class="product-badge">${product.badge}</div>`;
        }

        // Aggiungiamo un pezzo di HTML per questo prodotto alla nostra variabile di testo.
        // I "backtick" ( ` ) ci permettono di andare a capo e inserire variabili con ${...}
        htmlContent += `
            <div class="product-card" data-category="${product.category}" data-name="${product.name.toLowerCase()}" data-price="${product.price}">
                
                <div class="product-image" onclick="openProductModal(${product.id})">
                    ${product.emoji}
                    ${badgeHtml}
                </div>
                
                <div class="product-content">
                    <div class="product-category">${getCategoryName(product.category)}</div>
                    <h3 class="product-title">${product.name}</h3>
                    <p class="product-description">${product.description}</p>
                    
                    <div class="product-footer">
                        <span class="product-price">€${product.price.toFixed(2)}</span>
                        <button class="btn-add-cart" onclick="addToCart(${product.id})">
                            Aggiungi
                        </button>
                    </div>
                </div>

            </div>
        `;
    });

    // Infine, inseriamo tutto l'HTML creato dentro il contenitore sulla pagina
    container.innerHTML = htmlContent;
}

// Una piccola funzione di aiuto per scrivere i nomi delle categorie in bella copia
function getCategoryName(category) {
    if (category === 'alimentari') return 'Alimentari';
    if (category === 'artigianato') return 'Artigianato';
    if (category === 'bevande') return 'Bevande';
    if (category === 'conserve') return 'Conserve';
    return category; // Se non la trova, restituisce il nome originale
}

// ====================================
// FILTRI E RICERCA
// ====================================

// Nasconde o mostra le carte in base alla categoria scelta nel menu a tendina
function applyFilters() {
    const selectedCategory = document.getElementById('category-filter').value;
    const cards = document.querySelectorAll('.product-card'); // Prende tutte le carte

    // Controlla ogni singola carta
    cards.forEach(function(card) {
        const cardCategory = card.getAttribute('data-category');
        
        // Se la categoria è 'all' (tutte) oppure combacia con quella scelta, mostrala
        if (selectedCategory === 'all' || cardCategory === selectedCategory) {
            card.style.display = 'block'; // Mostra
        } else {
            card.style.display = 'none';  // Nascondi
        }
    });
}

// Cerca i prodotti in base a quello che l'utente scrive nella barra di ricerca
function searchProducts() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const cards = document.querySelectorAll('.product-card');

    cards.forEach(function(card) {
        const productName = card.getAttribute('data-name');
        
        // Se il nome del prodotto contiene le lettere cercate, mostralo
        if (productName.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// ====================================
// GESTIONE DEL CARRELLO
// ====================================

// Aggiunge un prodotto al carrello quando clicchi "Aggiungi"
function addToCart(productId) {
    // 1. Troviamo il prodotto giusto nel nostro database usando il suo ID
    const product = products.find(p => p.id === productId);
    if (!product) return; // Se non esiste, fermati qui

    // 2. Controlliamo se c'è già nel carrello
    const existingItem = cart.find(item => item.id === productId);

    if (existingItem) {
        // Se c'è già, aumentiamo solo la quantità
        existingItem.quantity++;
    } else {
        // Se non c'è, lo aggiungiamo al carrello con quantità 1
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            emoji: product.emoji,
            quantity: 1
        });
    }

    // 3. Salviamo le modifiche e aggiorniamo lo schermo
    saveCart();
    updateCartUI();
    showCartFeedback(); // Fa un piccolo effetto visivo sull'icona
}

// Rimuove completamente un prodotto dal carrello
function removeFromCart(productId) {
    // Filtriamo il carrello: teniamo tutti i prodotti TRANNE quello con questo ID
    cart = cart.filter(item => item.id !== productId);
    
    saveCart();
    updateCartUI();
}

// Cambia la quantità (+ o -) di un prodotto nel carrello
function updateQuantity(productId, change) {
    const item = cart.find(item => item.id === productId);
    if (!item) return;

    item.quantity += change; // Aggiunge o sottrae 1

    // Se la quantità arriva a 0, rimuovi il prodotto
    if (item.quantity <= 0) {
        removeFromCart(productId);
    } else {
        saveCart();
        updateCartUI();
    }
}

// Aggiorna l'interfaccia visiva del carrello (totale, numero oggetti, lista HTML)
function updateCartUI() {
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    const cartCount = document.getElementById('cart-count');
    
    // Calcoliamo quanti oggetti ci sono in totale
    let totalItems = 0;
    cart.forEach(item => totalItems += item.quantity);
    cartCount.textContent = totalItems;

    // Calcoliamo il prezzo totale
    let totalPrice = 0;
    cart.forEach(item => totalPrice += (item.price * item.quantity));
    cartTotal.textContent = `€${totalPrice.toFixed(2)}`; // toFixed(2) aggiunge i due decimali

    // Disegniamo i prodotti nel carrello laterale
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p>Il carrello è vuoto</p>';
    } else {
        let cartHtml = '';
        cart.forEach(item => {
            cartHtml += `
                <div class="cart-item" style="display:flex; justify-content:space-between; margin-bottom:10px;">
                    <div>${item.emoji} ${item.name}</div>
                    <div>
                        <button onclick="updateQuantity(${item.id}, -1)">-</button>
                        <span>${item.quantity}</span>
                        <button onclick="updateQuantity(${item.id}, 1)">+</button>
                        <button onclick="removeFromCart(${item.id})">X</button>
                    </div>
                </div>
            `;
        });
        cartItemsContainer.innerHTML = cartHtml;
    }
}

// ====================================
// FINESTRA MODALE (Dettaglio Prodotto)
// ====================================

// Apre la finestra grande quando clicchi sull'immagine di un prodotto
function openProductModal(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    const modal = document.getElementById('product-modal');
    const modalBody = document.getElementById('modal-body');

    // Costruiamo l'interno della finestra modale
    modalBody.innerHTML = `
        <div style="text-align: center;">
            <div style="font-size: 6rem; margin-bottom: 1rem;">${product.emoji}</div>
            <h2 style="color: #1e3a2b; margin-bottom: 1rem;">${product.name}</h2>
            <p style="color: #666; line-height: 1.7; margin-bottom: 1.5rem;">${product.description}</p>
            <div style="font-size: 2rem; font-weight: bold; color: #4a7c59; margin-bottom: 1.5rem;">
                €${product.price.toFixed(2)}
            </div>
            <button class="btn-add-cart" style="padding: 1rem 2rem;" onclick="addToCart(${product.id}); closeProductModal();">
                Aggiungi al Carrello
            </button>
        </div>
    `;

    modal.classList.add('active'); // Mostra la modale
}

function closeProductModal() {
    const modal = document.getElementById('product-modal');
    modal.classList.remove('active'); // Nasconde la modale
}

// Chiude la modale se premi il tasto "ESC" sulla tastiera
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeProductModal();
    }
});

// ====================================
// MEMORIA DEL BROWSER (LOCALSTORAGE)
// ====================================

// Salva il carrello nella memoria del browser così se ricarichi la pagina non lo perdi
function saveCart() {
    // Trasformiamo la nostra lista in un testo (JSON) e lo salviamo
    localStorage.setItem('molinoPalloneCart', JSON.stringify(cart));
}

// Carica il carrello dalla memoria quando si apre la pagina
function loadCart() {
    const savedCart = localStorage.getItem('molinoPalloneCart');
    if (savedCart) {
        // Ritrasformiamo il testo salvato in una vera lista Javascript
        cart = JSON.parse(savedCart);
    }
}

// ====================================
// EXTRA (Animazioni e messaggi)
// ====================================

// Fa ingrandire e rimpicciolire il pulsante del carrello quando aggiungi qualcosa
function showCartFeedback() {
    const cartIcon = document.querySelector('.cart-toggle');
    if (cartIcon) {
        cartIcon.style.transform = 'scale(1.2)';
        // Aspetta 300 millisecondi (0.3 secondi) e poi lo rimette normale
        setTimeout(function() {
            cartIcon.style.transform = 'scale(1)';
        }, 300);
    }
}

// Finestra finta per il checkout
function checkout() {
    if (cart.length === 0) {
        alert('Il carrello è vuoto!');
        return;
    }
    alert('Grazie per l\'ordine! Questo è un checkout simulato.');
}