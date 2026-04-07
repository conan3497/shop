<?php
// ==========================================
// SEZIONE PHP - Gestione del Checkout
// ==========================================
// Questa parte di codice viene eseguita dal server PRIMA che la pagina venga mostrata all'utente.

$messaggio_checkout = ""; // Variabile vuota che conterrà il nostro messaggio di successo

// Controlliamo se la pagina ha ricevuto dei dati tramite il metodo "POST" (cioè se l'utente ha cliccato acquista)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkout_inviato'])) {
    
    // Prendiamo i dati del carrello che JavaScript ci ha inviato in formato JSON
    // json_decode li trasforma in un Array che PHP può leggere facilmente
    $dati_carrello = json_decode($_POST['dati_carrello'], true);
    
    $totale_ordine = 0;
    $lista_oggetti = "";

    // Scorriamo ogni prodotto comprato per calcolare il totale e creare un riepilogo
    if (is_array($dati_carrello)) {
        foreach ($dati_carrello as $prodotto) {
            $totale_ordine += ($prodotto['price'] * $prodotto['quantity']);
            $lista_oggetti .= "<li>" . htmlspecialchars($prodotto['name']) . " (Quantità: " . $prodotto['quantity'] . ")</li>";
        }
    }

    // Creiamo il messaggio in HTML che mostreremo all'utente
    $messaggio_checkout = "
        <div style='background-color: #d4edda; color: #155724; padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb;'>
            <h3>🎉 Ordine completato con successo!</h3>
            <p>Grazie per il tuo acquisto. Ecco il riepilogo:</p>
            <ul>$lista_oggetti</ul>
            <strong>Totale pagato: €" . number_format($totale_ordine, 2) . "</strong>
        </div>
    ";
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevShop - Magliette & Gadget</title>
    
    <!-- ========================================== -->
    <!-- SEZIONE CSS - Lo Stile della pagina        -->
    <!-- ========================================== -->
    <style>
        /* Reset di base: toglie margini e padding predefiniti dei browser */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Stile del corpo della pagina */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Font più moderno */
            line-height: 1.6;
            color: #333; /* Grigio scuro per il testo */
            background-color: #f4f7f6; /* Sfondo grigio chiaro/azzurro */
        }

        /* --- HEADER E NAVIGAZIONE --- */
        header {
            background: linear-gradient(135deg, #2c3e50, #3498db); /* Sfondo sfumato blu/grigio */
            color: white;
            padding: 1rem 0;
            position: fixed; /* Mantiene la barra in alto anche scorrendo */
            width: 100%;
            top: 0;
            z-index: 1000; /* Assicura che stia sopra agli altri elementi */
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        nav {
            display: flex;
            justify-content: space-between; /* Spinge il logo a sinistra e i link a destra */
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ecf0f1;
        }

        .nav-links {
            display: flex;
            list-style: none; /* Toglie i puntini della lista */
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none; /* Toglie la sottolineatura ai link */
            transition: color 0.3s ease; /* Effetto di transizione morbido */
        }

        .nav-links a:hover {
            color: #f1c40f; /* Diventa giallo quando passi col mouse */
        }

        /* Il pallino rosso con il numero di oggetti nel carrello */
        .cart-badge {
            background: #e74c3c;
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: bold;
            margin-left: 0.3rem;
        }

        .cart-toggle {
            display: none; /* Nascosto su PC, si vede solo da cellulare */
            background: #2980b9;
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
        }

        /* --- CONTENUTO PRINCIPALE --- */
        main {
            margin-top: 90px; /* Spazio per non far coprire il contenuto dall'header fisso */
            padding: 2rem;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Banner principale (Hero) */
        .shop-hero {
            background: linear-gradient(135deg, #34495e, #7f8c8d);
            color: white;
            padding: 3rem 2rem;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* Sezione filtri di ricerca */
        .filters-section {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .filters-container {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap; /* Va a capo se lo schermo è piccolo */
            align-items: center;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            flex: 1;
            min-width: 200px;
        }

        .filter-group select,
        .filter-group input {
            padding: 0.8rem;
            border: 2px solid #bdc3c7;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        /* --- GRIGLIA PRODOTTI --- */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); /* Griglia responsiva automatica */
            gap: 2rem;
        }

        /* Singola carta del prodotto */
        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-5px); /* Sale leggermente quando ci passi sopra */
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .product-image {
            height: 200px;
            background: linear-gradient(45deg, #bdc3c7, #ecf0f1); /* Sfondo grigio neutro per i gadget */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem; /* Grandezza dell'emoji */
            position: relative;
        }

        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #e74c3c;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .product-badge.new {
            background: #27ae60; /* Verde se è nuovo */
        }

        .product-content {
            padding: 1.5rem;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2980b9;
        }

        /* Bottoni generali */
        .btn-add-cart {
            background: #2980b9;
            color: white;
            border: none;
            padding: 0.7rem 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-add-cart:hover {
            background: #1f618d;
            transform: scale(1.05); /* Si ingrandisce un po' */
        }

        /* --- CARRELLO LATERALE --- */
        .cart-sidebar {
            position: fixed;
            right: -400px; /* Nascosto fuori dallo schermo di default */
            top: 0;
            width: 400px;
            height: 100vh;
            background: white;
            box-shadow: -5px 0 20px rgba(0,0,0,0.2);
            z-index: 2000;
            transition: right 0.3s ease; /* Effetto scorrimento */
            display: flex;
            flex-direction: column;
        }

        .cart-sidebar.active {
            right: 0; /* Lo fa apparire sullo schermo */
        }
        
        /* Contenitore interno del carrello per far scorrere gli oggetti se sono tanti */
        .cart-body {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        /* Singolo elemento nel carrello */
        .cart-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid #ecf0f1;
            margin-bottom: 1rem;
        }

        .cart-item-image {
            width: 60px;
            height: 60px;
            background: #ecf0f1;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .cart-footer {
            padding: 1.5rem;
            border-top: 2px solid #ecf0f1;
        }

        .btn-checkout {
            width: 100%;
            background: #27ae60; /* Verde per l'acquisto */
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            margin-bottom: 0.5rem;
        }

        /* Overlay scuro dietro il carrello o la modale */
        .cart-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1500;
            display: none;
        }
        .cart-overlay.active { display: block; }

        /* --- MODALE (Finestra popup prodotto) --- */
        .modal {
            display: none;
            position: fixed;
            z-index: 3000;
            left: 0; top: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.7);
        }
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            max-width: 600px;
            width: 90%;
            position: relative;
        }

        /* Stili extra per nascondere testi e layout mobile */
        .no-products { display: none; text-align: center; padding: 3rem; }
        footer { background: #2c3e50; color: white; text-align: center; padding: 2rem 0; margin-top: 4rem; }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .cart-toggle { display: block; }
            .cart-sidebar { width: 100%; right: -100%; }
        }
    </style>
</head>
<body>
    <!-- Header e Menu Navigazione -->
    <header>
        <nav>
            <div class="logo">💻 DevShop</div>
            <ul class="nav-links">
                <li><a href="#">Home</a></li>
                <li><a href="#prodotti">Prodotti</a></li>
                <li><a href="#" onclick="toggleCart()">Carrello <span id="cart-count" class="cart-badge">0</span></a></li>
            </ul>
            <!-- Bottone visibile solo su cellulare -->
            <button class="cart-toggle" onclick="toggleCart()">
                🛒 <span id="cart-count-mobile" class="cart-badge">0</span>
            </button>
        </nav>
    </header>

    <main>
        <!-- Questo stampa il messaggio di successo generato da PHP (se esiste) -->
        <?php echo $messaggio_checkout; ?>

        <!-- Intestazione della pagina -->
        <section class="shop-hero">
            <h1>🛒 DevShop</h1>
            <p>Le migliori magliette e gadget per programmatori e nerd!</p>
        </section>

        <!-- Filtri per cercare i prodotti -->
        <section class="filters-section">
            <div class="filters-container">
                <div class="filter-group">
                    <label>Categoria:</label>
                    <select id="category-filter" onchange="applyFilters()">
                        <option value="all">Tutte le Categorie</option>
                        <option value="abbigliamento">Abbigliamento</option>
                        <option value="gadget">Gadget & Ufficio</option>
                        <option value="accessori">Accessori</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Ordina per:</label>
                    <select id="sort-filter" onchange="applySorting()">
                        <option value="default">Predefinito</option>
                        <option value="price-asc">Prezzo: dal più economico</option>
                        <option value="price-desc">Prezzo: dal più costoso</option>
                        <option value="name">Nome A-Z</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Cerca:</label>
                    <input type="text" id="search-input" placeholder="Cerca una maglietta..." oninput="searchProducts()">
                </div>
            </div>
        </section>

        <!-- Qui JavaScript inietterà i nostri prodotti -->
        <section class="products-section" id="prodotti">
            <div class="products-grid" id="products-container">
                <!-- I prodotti vengono caricati dinamicamente da JS -->
            </div>

            <!-- Messaggio mostrato se la ricerca non trova nulla -->
            <div id="no-products" class="no-products">
                <div style="font-size: 4rem;">🔍</div>
                <h3>Nessun prodotto trovato</h3>
                <p>Prova a cercare qualcos'altro.</p>
            </div>
        </section>
    </main>

    <!-- Menu laterale del Carrello -->
    <div id="cart-sidebar" class="cart-sidebar">
        <div style="display: flex; justify-content: space-between; padding: 1.5rem; border-bottom: 1px solid #ccc;">
            <h2>🛒 Il Tuo Carrello</h2>
            <button onclick="toggleCart()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">×</button>
        </div>

        <div class="cart-body" id="cart-items">
            <!-- Gli oggetti del carrello finiscono qui tramite JS -->
        </div>

        <div class="cart-footer">
            <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold; margin-bottom: 1rem;">
                <span>Totale:</span>
                <span id="cart-total">€0.00</span>
            </div>
            
            <!-- FORM NASCOSTO PER PHP -->
            <!-- Questo form è invisibile all'utente. Viene compilato da JS e inviato a PHP quando si clicca acquista -->
            <form id="form-checkout-php" method="POST" action="">
                <!-- Qui JS scriverà i dati del carrello in formato testo (JSON) -->
                <input type="hidden" name="dati_carrello" id="input_dati_carrello">
                <!-- Questo dice a PHP che il form è stato inviato -->
                <input type="hidden" name="checkout_inviato" value="1">
            </form>

            <button class="btn-checkout" onclick="inviaA_PHP()">Procedi all'Acquisto</button>
            <button onclick="clearCart()" style="width:100%; padding:0.8rem; background:transparent; border: 1px solid #e74c3c; color: #e74c3c; cursor:pointer; border-radius:8px;">Svuota Carrello</button>
        </div>
    </div>

    <!-- Sfondo scuro per chiudere carrello/modale cliccando fuori -->
    <div id="cart-overlay" class="cart-overlay" onclick="toggleCart()"></div>

    <!-- Finestra Pop-up (Modale) del singolo prodotto -->
    <div id="product-modal" class="modal">
        <div class="modal-content">
            <button onclick="closeProductModal()" style="position:absolute; right:15px; top:15px; background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
            <div id="modal-body">
                <!-- Contenuto caricato da JS -->
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 DevShop. Creato con ☕ e ❤️.</p>
    </footer>

    <!-- ========================================== -->
    <!-- SEZIONE JAVASCRIPT - La logica della pagina-->
    <!-- ========================================== -->
    <script>
        // 1. IL NOSTRO DATABASE PRODOTTI (Array di oggetti)
        // Ho sostituito il cibo con magliette e gadget tecnologici
        const products = [
            {
                id: 1,
                name: "T-Shirt 'Hello World'",
                category: "abbigliamento",
                price: 19.99,
                emoji: "👕",
                description: "La maglietta classica per ogni sviluppatore alle prime armi. Cotone 100% organico.",
                badge: "Bestseller"
            },
            {
                id: 2,
                name: "Tazza 'Coffee to Code'",
                category: "gadget",
                price: 12.50,
                emoji: "☕",
                description: "Trasforma il tuo caffè in codice funzionante. Capacità 350ml, adatta al microonde.",
                badge: null
            },
            {
                id: 3,
                name: "Felpa Hoodie 'Error 404'",
                category: "abbigliamento",
                price: 45.00,
                emoji: "🧥",
                description: "Calda felpa con cappuccio. La motivazione (e il bug) non è stata trovata.",
                badge: "Nuovo"
            },
            {
                id: 4,
                name: "Pack 50 Stickers Dev",
                category: "accessori",
                price: 8.90,
                emoji: "👾",
                description: "Riempi il tuo portatile di adesivi! Linguaggi, framework e meme tech.",
                badge: null
            },
            {
                id: 5,
                name: "Tappetino Mouse XL",
                category: "gadget",
                price: 22.00,
                emoji: "🖱️",
                description: "Tappetino gigante con scorciatoie da tastiera stampate. Base antiscivolo.",
                badge: "Utile"
            },
            {
                id: 6,
                name: "Zaino Porta PC Tech",
                category: "accessori",
                price: 55.00,
                emoji: "🎒",
                description: "Zaino impermeabile con porta USB integrata e scomparto imbottito per laptop 15.6\".",
                badge: "Premium"
            }
        ];

        // Questa variabile memorizza i prodotti che l'utente vuole comprare
        let cart = [];

        // 2. INIZIALIZZAZIONE DELLA PAGINA
        // Quando la pagina ha finito di caricare HTML, eseguiamo queste funzioni
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts(); // Disegna i prodotti sullo schermo
            loadCart();     // Controlla se c'era roba nel carrello salvata in precedenza
            updateCartUI(); // Aggiorna i numeri e il testo del carrello
        });

        // 3. FUNZIONI PER MOSTRARE I PRODOTTI
        function loadProducts() {
            const container = document.getElementById('products-container');
            container.innerHTML = ''; // Pulisce il contenitore prima di riempirlo

            // Per ogni prodotto nel database, crea una "carta" visiva
            products.forEach(product => {
                const productCard = createProductCard(product);
                container.appendChild(productCard);
            });
            checkNoProducts();
        }

        // Crea l'HTML per il singolo riquadro del prodotto
        function createProductCard(product) {
            const card = document.createElement('div');
            card.className = 'product-card';
            // Salviamo dati invisibili nell'HTML per facilitare filtri e ordinamenti
            card.dataset.category = product.category;
            card.dataset.name = product.name.toLowerCase();
            card.dataset.price = product.price;

            // Il simbolo ` permette di scrivere HTML su più righe in JavaScript
            card.innerHTML = `
                <div class="product-image" onclick="openProductModal(${product.id})">
                    ${product.emoji}
                    ${product.badge ? `<div class="product-badge ${product.badge === 'Nuovo' ? 'new' : ''}">${product.badge}</div>` : ''}
                </div>
                <div class="product-content">
                    <div style="color: #3498db; font-size: 0.8rem; text-transform: uppercase;">${getCategoryName(product.category)}</div>
                    <h3 style="margin-bottom: 0.5rem; color: #2c3e50;">${product.name}</h3>
                    <p style="color: #7f8c8d; font-size: 0.9rem; margin-bottom: 1rem;">${product.description}</p>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee; padding-top: 1rem;">
                        <span class="product-price">€${product.price.toFixed(2)}</span>
                        <button class="btn-add-cart" onclick="addToCart(${product.id})">Aggiungi</button>
                    </div>
                </div>
            `;
            return card;
        }

        // Converte le categorie tecniche in nomi belli da leggere
        function getCategoryName(category) {
            const categories = {
                'abbigliamento': 'Abbigliamento',
                'gadget': 'Gadget & Ufficio',
                'accessori': 'Accessori'
            };
            return categories[category] || category;
        }

        // 4. FUNZIONI DI RICERCA E FILTRO
        function applyFilters() {
            const category = document.getElementById('category-filter').value;
            const cards = document.querySelectorAll('.product-card');

            cards.forEach(card => {
                // Mostra la carta solo se la categoria coincide o se è "all" (tutte)
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
            checkNoProducts();
        }

        function searchProducts() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const cards = document.querySelectorAll('.product-card');

            cards.forEach(card => {
                if (card.dataset.name.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
            checkNoProducts();
        }

        function checkNoProducts() {
            const cards = document.querySelectorAll('.product-card');
            // Cerca quante carte sono visibili
            const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
            const noProductsDiv = document.getElementById('no-products');

            // Se zero, mostra il messaggio di errore
            if (visibleCards.length === 0) {
                noProductsDiv.style.display = 'block';
            } else {
                noProductsDiv.style.display = 'none';
            }
        }

        function applySorting() {
            const sortType = document.getElementById('sort-filter').value;
            const container = document.getElementById('products-container');
            const cards = Array.from(container.querySelectorAll('.product-card'));

            // Riordina l'array di carte in base al criterio scelto
            cards.sort((a, b) => {
                if (sortType === 'price-asc') return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                if (sortType === 'price-desc') return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                if (sortType === 'name') return a.dataset.name.localeCompare(b.dataset.name);
                return 0;
            });

            // Riappende le carte nell'HTML nel nuovo ordine
            cards.forEach(card => container.appendChild(card));
        }

        // 5. GESTIONE CARRELLO (Logica principale)
        function addToCart(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) return;

            // Controlla se il prodotto è già nel carrello
            const existingItem = cart.find(item => item.id === productId);

            if (existingItem) {
                existingItem.quantity++; // Se c'è, aumenta solo la quantità
            } else {
                // Altrimenti lo aggiunge con quantità 1
                cart.push({ ...product, quantity: 1 });
            }

            saveCart();     // Salva nella memoria del browser
            updateCartUI(); // Aggiorna grafica
            
            // Effetto visivo sull'icona
            const cartIcon = document.querySelector('.cart-toggle');
            if(cartIcon) {
                cartIcon.style.transform = 'scale(1.2)';
                setTimeout(() => cartIcon.style.transform = 'scale(1)', 300);
            }
        }

        function removeFromCart(productId) {
            // Ricrea il carrello mantenendo solo i prodotti DIVERSI dall'id da rimuovere
            cart = cart.filter(item => item.id !== productId);
            saveCart();
            updateCartUI();
        }

        function clearCart() {
            if (confirm('Sei sicuro di voler svuotare il carrello?')) {
                cart = [];
                saveCart();
                updateCartUI();
            }
        }

        function updateCartUI() {
            const cartItemsContainer = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');
            const cartCount = document.getElementById('cart-count');
            const cartCountMobile = document.getElementById('cart-count-mobile');

            // Calcola numero oggetti e costo totale
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const totalCosto = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            cartCount.textContent = totalItems;
            cartCountMobile.textContent = totalItems;
            cartTotal.textContent = `€${totalCosto.toFixed(2)}`;

            if (cart.length === 0) {
                cartItemsContainer.innerHTML = '<p style="text-align:center; margin-top:2rem; color:#7f8c8d;">Il carrello è vuoto</p>';
            } else {
                // Costruisce l'HTML per ogni elemento nel carrello
                cartItemsContainer.innerHTML = cart.map(item => `
                    <div class="cart-item">
                        <div class="cart-item-image">${item.emoji}</div>
                        <div style="flex:1;">
                            <div style="font-weight:bold; color:#2c3e50;">${item.name}</div>
                            <div style="color:#2980b9;">€${item.price.toFixed(2)}</div>
                            <div style="margin-top:0.5rem; display:flex; gap:10px; align-items:center;">
                                <span>Qtà: ${item.quantity}</span>
                                <button onclick="removeFromCart(${item.id})" style="background:#e74c3c; color:white; border:none; padding:3px 8px; border-radius:4px; cursor:pointer; font-size:0.8rem;">Rimuovi</button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        }

        // Apre e chiude la tendina laterale
        function toggleCart() {
            document.getElementById('cart-sidebar').classList.toggle('active');
            document.getElementById('cart-overlay').classList.toggle('active');
        }

        // 6. INVIO A PHP (Checkout)
        function inviaA_PHP() {
            if (cart.length === 0) {
                alert('Il carrello è vuoto! Aggiungi dei prodotti prima.');
                return;
            }

            // Trasformiamo l'array JavaScript (cart) in una stringa di testo (JSON)
            // Questo perché PHP e HTML capiscono solo testo, non le variabili JS
            const datiFormattati = JSON.stringify(cart);
            
            // Inseriamo questa stringa dentro l'input invisibile del nostro form HTML
            document.getElementById('input_dati_carrello').value = datiFormattati;
            
            // Dopo aver copiato i dati, svuotiamo il carrello di JavaScript per il prossimo utente
            cart = [];
            saveCart();
            
            // Inviamo il form al server (Questo ricaricherà la pagina e farà partire il codice PHP in alto!)
            document.getElementById('form-checkout-php').submit();
        }

        // 7. FINESTRA POPUP DEL PRODOTTO
        function openProductModal(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) return;

            document.getElementById('modal-body').innerHTML = `
                <div style="text-align: center;">
                    <div style="font-size: 6rem; margin-bottom: 1rem;">${product.emoji}</div>
                    <h2 style="color: #2c3e50; margin-bottom: 1rem;">${product.name}</h2>
                    <p style="color: #7f8c8d; line-height: 1.7; margin-bottom: 1.5rem;">${product.description}</p>
                    <div style="font-size: 2rem; font-weight: bold; color: #2980b9; margin-bottom: 1.5rem;">
                        €${product.price.toFixed(2)}
                    </div>
                    <button class="btn-add-cart" style="padding: 1rem 2rem; font-size: 1.1rem;" onclick="addToCart(${product.id}); closeProductModal();">
                        Aggiungi al Carrello
                    </button>
                </div>
            `;
            document.getElementById('product-modal').classList.add('active');
        }

        function closeProductModal() {
            document.getElementById('product-modal').classList.remove('active');
        }

        // 8. SALVATAGGIO NELLA MEMORIA DEL BROWSER
        // Così se l'utente chiude la pagina e la riapre, non perde il carrello
        function saveCart() {
            localStorage.setItem('devShopCart', JSON.stringify(cart));
        }

        function loadCart() {
            const savedCart = localStorage.getItem('devShopCart');
            if (savedCart) {
                cart = JSON.parse(savedCart);
            }
        }
    </script>
</body>
</html>