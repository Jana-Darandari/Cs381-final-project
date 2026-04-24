document.addEventListener("DOMContentLoaded", () => {
  const STORAGE_KEY = "bubble_market_data";
  const USERS_KEY = "bubble_users_data";
  const MESSAGES_KEY = "bubble_messages_data";
  const CURRENT_USER_KEY = "bubble_current_user"; // Replaces the old ROLE_KEY

  // 1. FAKE DATABASE SETUP (Relational Tables)
  if (!localStorage.getItem(USERS_KEY)) {
    localStorage.setItem(USERS_KEY, JSON.stringify([
      { user_id: 1, email: "admin@campus.edu", password: "password", role: "admin" },
      { user_id: 2, email: "student@campus.edu", password: "password", role: "student" }
    ]));
  }
  
  if (!localStorage.getItem(STORAGE_KEY)) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify([
      { id: 1, seller_id: 2, title: "Calculus Textbook", category: "Books", price: 120, description: "Barely used.", item_image: "", status: "available" },
      { id: 2, seller_id: 1, title: "iPad Air", category: "Tablets", price: 1800, description: "Comes with case.", item_image: "", status: "available" }
    ]));
  }

  if (!localStorage.getItem(MESSAGES_KEY)) {
    localStorage.setItem(MESSAGES_KEY, JSON.stringify([]));
  }

  let users = JSON.parse(localStorage.getItem(USERS_KEY));
  let products = JSON.parse(localStorage.getItem(STORAGE_KEY));
  let messages = JSON.parse(localStorage.getItem(MESSAGES_KEY));
  let currentUser = JSON.parse(localStorage.getItem(CURRENT_USER_KEY)); // Object: { user_id, role }

  // 2. AUTHENTICATION & SECURITY
  const currentRole = currentUser ? currentUser.role : "guest";

  if (window.location.pathname.includes("admin-panel") && currentRole !== "admin") {
    alert("Access Denied: You must be an Admin.");
    window.location.replace("index.html");
    return;
  }
  
  if ((window.location.pathname.includes("add-product") || window.location.pathname.includes("my-listings") || window.location.pathname.includes("messages")) && currentRole === "guest") {
    alert("Please log in to access this page.");
    window.location.replace("login.html");
    return;
  }

  document.querySelectorAll(".admin-only").forEach(el => el.style.display = currentRole === "admin" ? "block" : "none");
  document.querySelectorAll(".logged-in-only").forEach(el => el.style.display = currentRole !== "guest" ? "block" : "none");
  document.querySelectorAll(".guest-only").forEach(el => el.style.display = currentRole === "guest" ? "block" : "none");

  // Highlight Active Nav
  const path = window.location.pathname;
  if (path.includes("add-product")) document.getElementById("nav-add")?.classList.add("active");
  else if (path.includes("my-listings")) document.getElementById("nav-mine")?.classList.add("active");
  else if (path.includes("admin-panel")) document.getElementById("nav-admin")?.classList.add("active");
  else if (path.includes("login")) document.getElementById("nav-login")?.classList.add("active");
  else if (path.includes("register")) document.getElementById("nav-register")?.classList.add("active");
  else if (path.includes("messages")) document.getElementById("nav-messages")?.classList.add("active");
  else document.getElementById("nav-index")?.classList.add("active");

  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", (e) => {
      e.preventDefault();
      localStorage.removeItem(CURRENT_USER_KEY);
      window.location.href = "login.html";
    });
  }

  // 3. REGISTRATION LOGIC
  const registerForm = document.getElementById("registerForm");
  if (registerForm) {
    registerForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const email = document.getElementById("regEmail").value;
      const pass = document.getElementById("regPassword").value;
      const role = document.getElementById("regRole").value;

      if(users.find(u => u.email === email)) {
        alert("Email already exists!");
        return;
      }

      const newUser = { user_id: Date.now(), email: email, password: pass, role: role };
      users.push(newUser);
      localStorage.setItem(USERS_KEY, JSON.stringify(users));
      alert("Registration successful! Please log in.");
      window.location.href = "login.html";
    });
  }

  // 4. LOGIN LOGIC (Validates against Users array)
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    // Hide the old role switcher from the original UI since auth checks the DB now
    const roleBtns = document.querySelectorAll(".role-btn");
    roleBtns.forEach(btn => btn.style.display = 'none');

    loginForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const emailInput = loginForm.querySelector('input[type="email"]').value;
      const passInput = loginForm.querySelector('input[type="password"]').value;

      const user = users.find(u => u.email === emailInput && u.password === passInput);
      
      if (!user) {
        alert("Invalid email or password.");
        return;
      }

      localStorage.setItem(CURRENT_USER_KEY, JSON.stringify({ user_id: user.user_id, role: user.role }));
      window.location.href = user.role === "admin" ? "admin-panel.html" : "index.html";
    });
  }

  // 5. HOMEPAGE SEARCH & FILTER (Supports Images)
  const productsGrid = document.getElementById("productsGrid");
  if (productsGrid) {
    function renderProducts() {
      let filtered = products;
      const text = document.getElementById("searchInput")?.value.toLowerCase() || "";
      const cat = document.getElementById("categoryFilter")?.value || "";
      const stat = document.getElementById("statusFilter")?.value || "";
      const price = document.getElementById("priceFilter")?.value || "";

      if (text) filtered = filtered.filter(p => p.title.toLowerCase().startsWith(text));
      if (cat) filtered = filtered.filter(p => p.category === cat);
      if (stat) filtered = filtered.filter(p => p.status === stat);
      if (price) {
        filtered = filtered.filter(p => {
          if (price === "0-300") return p.price <= 300;
          if (price === "301-1000") return p.price > 300 && p.price <= 1000;
          if (price === "1000+") return p.price > 1000;
          return true;
        });
      }

      if(filtered.length === 0) {
        productsGrid.innerHTML = `<h3 style="color:var(--muted); grid-column: 1/-1; text-align: center;">No products match your search.</h3>`;
        return;
      }

      productsGrid.innerHTML = filtered.map(p => `
        <a href="product-details.html?id=${p.id}" class="card" style="display:block;">
          <div class="product-image" ${p.item_image ? `style="background-image:url('${p.item_image}'); background-size:cover; background-position:center;"` : ''}>
            ${!p.item_image ? (p.category === 'Books' ? '📚' : p.category === 'Tablets' ? '📱' : '📦') : ''}
          </div>
          <h3 style="margin: 10px 0 5px;">${p.title}</h3>
          <div style="color: #6f78a8; font-size: 0.9rem;">${p.category} • ${p.status}</div>
          <div class="product-price">${p.price} SR</div>
        </a>
      `).join("");
    }
    
    document.querySelectorAll(".search-bar input, .search-bar select").forEach(el => {
      el.addEventListener("input", renderProducts);
    });
    renderProducts();
  }

  // 6. ADD PRODUCT (Links item to seller_id)
  const addForm = document.getElementById("addForm");
  if (addForm) {
    addForm.addEventListener("submit", (e) => {
      e.preventDefault();
      
      const newItem = {
        id: Date.now(),
        seller_id: currentUser.user_id,
        title: document.getElementById("addTitle").value,
        category: document.getElementById("addCategory").value,
        price: Number(document.getElementById("addPrice").value),
        description: document.getElementById("addDescription").value,
        item_image: document.getElementById("addImage").value,
        status: "available"
      };

      products.unshift(newItem);
      localStorage.setItem(STORAGE_KEY, JSON.stringify(products));
      alert("Item added successfully!");
      window.location.href = "index.html";
    });
  }

  // 7. ADMIN PANEL
  const adminListings = document.getElementById("adminListings");
  if (adminListings) {
    adminListings.innerHTML = products.map(p => `
      <div class="card" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding: 15px 30px;">
        <h3 style="margin:0;">${p.title} <span style="font-size:0.9rem; color:#6f78a8;">(${p.status})</span></h3>
        <button class="btn" style="background: #ff85a2; padding: 10px 20px;" onclick="deleteItem(${p.id})">Delete</button>
      </div>
    `).join("");

    window.deleteItem = function(id) {
      if(confirm("Delete this listing permanently?")) {
        products = products.filter(p => p.id !== id);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(products));
        window.location.reload(); 
      }
    };
  }

  // 8. MY LISTINGS (Filters by logged-in user's seller_id)
  const myListings = document.getElementById("myListings");
  if (myListings) {
    const myProducts = products.filter(p => p.seller_id === currentUser.user_id);
    
    if(myProducts.length === 0) {
      myListings.innerHTML = "<p>You haven't posted any items yet.</p>";
    } else {
      myListings.innerHTML = myProducts.map(p => `
        <div class="card" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding: 15px 30px;">
          <h3 style="margin:0;">${p.title} <span style="font-size:0.9rem; color:#6f78a8;">(Current: ${p.status})</span></h3>
          <button class="btn btn-ghost" style="padding: 10px 20px;" onclick="toggleStatus(${p.id})">
            Mark as ${p.status === 'available' ? 'Sold' : 'Available'}
          </button>
        </div>
      `).join("");
    }

    window.toggleStatus = function(id) {
      const product = products.find(p => p.id === id);
      product.status = product.status === 'available' ? 'sold' : 'available';
      localStorage.setItem(STORAGE_KEY, JSON.stringify(products));
      window.location.reload(); 
    };
  }

  // 9. PRODUCT DETAILS & MESSAGING
  const detailCard = document.getElementById("detailCard");
  if (detailCard) {
    const params = new URLSearchParams(window.location.search);
    const p = products.find(x => x.id === parseInt(params.get("id")));
    
    if (p) {
      detailCard.innerHTML = `
        <div class="product-image" style="height: 250px; font-size: 6rem; ${p.item_image ? `background-image:url('${p.item_image}'); background-size:cover; background-position:center;` : ''}">
          ${!p.item_image ? (p.category === 'Books' ? '📚' : p.category === 'Tablets' ? '📱' : '📦') : ''}
        </div>
        <h1 style="color: var(--bubble-blue); margin-bottom: 5px;">${p.title}</h1>
        <p style="color: var(--muted); font-size: 1.2rem; margin-top: 0;">Category: ${p.category} | Status: ${p.status}</p>
        <p style="margin: 20px 0; font-size: 1.1rem; text-align: left; background: var(--bubble-light); padding: 20px; border-radius: 15px;">${p.description || "No description."}</p>
        <div class="product-price" style="font-size: 1.5rem;">${p.price} SR</div>
        <br><br>
        
        ${currentUser && currentUser.user_id !== p.seller_id ? `
          <div style="text-align: left; background: #fff; padding: 20px; border-radius: 15px; margin-top: 20px; border: 2px solid var(--bubble-light);">
            <h3 style="margin-top:0; color: var(--bubble-blue);">Message Seller</h3>
            <textarea id="msgText" class="bubble-input" rows="3" placeholder="I'm interested in this!"></textarea>
            <button class="btn" style="margin-top: 10px; width: 100%;" onclick="sendMessage(${p.id}, ${p.seller_id})">Send Message</button>
          </div>
        ` : (currentUser ? '<p style="color:var(--muted); font-weight:bold;">This is your own listing.</p>' : '<p><a href="login.html" style="color:var(--bubble-blue); font-weight:bold;">Log in to message seller</a></p>')}
      `;
    } else {
      detailCard.innerHTML = `<h2>Product not found.</h2>`;
    }

    // Handle sending the message
    window.sendMessage = function(itemId, receiverId) {
      const text = document.getElementById("msgText").value;
      if(!text) return alert("Please type a message.");

      messages.push({
        message_id: Date.now(),
        sender_id: currentUser.user_id,
        receiver_id: receiverId,
        item_id: itemId,
        message_text: text,
        created_at: new Date().toLocaleString()
      });
      
      localStorage.setItem(MESSAGES_KEY, JSON.stringify(messages));
      alert("Message sent to seller!");
      document.getElementById("msgText").value = "";
    };
  }

  // 10. INBOX RENDERING
  const inboxContainer = document.getElementById("inboxContainer");
  if (inboxContainer) {
    const myMessages = messages.filter(m => m.receiver_id === currentUser.user_id);
    
    if(myMessages.length === 0) {
      inboxContainer.innerHTML = "<p style='color: var(--muted);'>Your inbox is empty.</p>";
    } else {
      inboxContainer.innerHTML = myMessages.reverse().map(m => {
        const item = products.find(p => p.id === m.item_id);
        const sender = users.find(u => u.user_id === m.sender_id);
        return `
          <div class="card" style="text-align:left; margin-bottom: 15px; border: 2px solid var(--bubble-light);">
            <h4 style="margin: 0 0 10px; color: var(--bubble-blue);">Regarding: ${item ? item.title : 'Deleted Item'}</h4>
            <p style="margin: 0 0 10px; font-size: 0.9rem;"><strong>From:</strong> ${sender ? sender.email : 'Unknown User'}</p>
            <p style="background: var(--bubble-light); padding: 15px; border-radius: 10px; margin: 0;">${m.message_text}</p>
            <small style="color: var(--muted); display:block; margin-top: 10px;">Sent: ${m.created_at}</small>
          </div>
        `;
      }).join("");
    }
  }
});