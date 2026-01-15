<?php
session_start();
include 'include/db_connect.php';

// Check if logged in
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_email'])) {
    header("Location: login_admin.php");
    exit;
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? 'admin@example.com';


$imgurUrls = [
    "Cappuccino"      => "https://i.imgur.com/YOmjA18.png",
    "Mocha"           => "https://i.imgur.com/tCJoQ27.png",
    "Latte"           => "https://i.imgur.com/tUP9FzY.png",
    "Espresso"        => "https://i.imgur.com/WUt528p.png",
    "Caramel Macchiato"=> "https://i.imgur.com/S9rFxze.png",
    "Iced Americano"  => "https://i.imgur.com/HPde9wI.png",
    "Chocolate Frappe"=> "https://i.imgur.com/dq4cgMQ.png",
    "Matcha Latte"    => "https://i.imgur.com/FX9C3qj.png",
    "Hazelnut Latte"  => "https://i.imgur.com/WBKKUXO.png",
    "Spanish Latte"   => "https://i.imgur.com/Kt94iBl.png",
    "Bagel"           => "https://i.imgur.com/w9d5TKM.png",
    "Croissant"       => "https://i.imgur.com/uKJNnD5.png",
    "Chocolate Croissant"=> "https://i.imgur.com/hjPNYdC.png",
    "Cinnamon Roll"   => "https://i.imgur.com/CecIfje.png",
    "Banana Bread"    => "https://i.imgur.com/cCMA4PB.png",
    "Blueberry Muffin"=> "https://i.imgur.com/0QURil1.png",
    "Cheese Ensaymada"=> "https://i.imgur.com/Kj7mNwi.png",
    "Garlic Bread"    => "https://i.imgur.com/e2H9Kad.png",
    "Ham Sandwich"    => "https://i.imgur.com/Qui7Ar1.png"
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Menu Management - AdminHub</title>

  <!-- Boxicons -->
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>

  <!-- Your CSS -->
  <link rel="stylesheet" href="style.css?v=22">

  <style>
    /* small adjustments for card UI */
    .brand img {
      width: 40px;
      height: auto;
      margin-right: 3px;
      vertical-align: middle;
    }
    main .controls {
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      margin-bottom:18px;
    }
    .btn-add {
      background: linear-gradient(90deg,#b8863b,#f0c27b);
      border: none;
      color: #222;
      padding: 10px 16px;
      border-radius: 8px;
      font-weight:700;
      cursor:pointer;
      box-shadow: 0 3px 8px rgba(0,0,0,0.12);
    }
    .btn-add:hover { opacity:0.95; }
    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
      gap: 18px;
      margin-top: 18px;
    }
    .card {
      background: #fff;
      border-radius: 12px;
      padding: 12px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.08);
      text-align: center;
    }
    .card img {
      width:100%;
      height:140px;
      object-fit:cover;
      border-radius:8px;
      margin-bottom:8px;
    }
    .card h4 { margin:6px 0 4px; color:#222; }
    .card p.desc { font-size:13px; color:#555; height:38px; overflow:hidden; margin:0 0 8px; }
    .card .meta { font-weight:700; color:#2b2b2b; margin-bottom:8px; }
    .card .cat { display:inline-block; font-size:12px; padding:4px 8px; border-radius:999px; background:#f3f3f3; color:#333; margin-bottom:8px; }
    .card .actions { display:flex; gap:8px; justify-content:center; }
    .action-edit, .action-delete {
      padding:8px 12px;
      border-radius:8px;
      color:#fff;
      text-decoration:none;
      font-weight:600;
      cursor:pointer;
      border:none;
    }
    .action-edit { background:#2a9d8f; }
    .action-delete { background:#d90429; }
    /* modal styles */
    .modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; z-index:9999; }
    .modal { width:100%; max-width:560px; background:#fff; padding:18px; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.2); }
    .modal h3 { margin-top:0; }
    .form-row { margin-bottom:12px; }
    .form-row input[type="text"], .form-row input[type="number"], .form-row textarea, .form-row select {
      width:100%; padding:10px; border-radius:8px; border:1px solid #ddd; box-sizing:border-box;
    }
    .form-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:8px; }
    .btn-cancel { background:#f3f4f6; color:#111; padding:8px 12px; border-radius:8px; cursor:pointer; border:none; }
    .btn-save { background:#3a86ff; color:#fff; padding:8px 12px; border-radius:8px; cursor:pointer; border:none; }
    .notice { padding:10px; background:#e6ffed; border-radius:8px; color:#0a662a; margin-bottom:10px; display:none; }
  </style>
</head>
<body>

  <!-- SIDEBAR -->
  <section id="sidebar">
    <a href="#" class="brand">
      <img src="img/logo.png">
      <span class="text">Midnight Brew</span>
    </a>
    <ul class="side-menu top">
      <li>
        <a href="index.php">
          <i class='bx bxs-dashboard' ></i>
          <span class="text">Dashboard</span>
        </a>
      </li>
      <li>
        <a href="orders.php">
          <i class='bx bxs-doughnut-chart' ></i>
          <span class="text">Orders</span>
        </a>
      </li>
      <li class="active">
        <a href="menu.php">
          <i class='bx bxs-coffee' ></i>
          <span class="text">Menu</span>
        </a>
      </li>
      <li>
        <a href="team.php">
          <i class='bx bxs-group' ></i>
          <span class="text">Team</span>
        </a>
      </li>
    </ul>
    <ul class="side-menu">
      <li>
        <a href="logout.php" class="logout">
          <i class='bx bxs-log-out-circle' ></i>
          <span class="text">Logout</span>
        </a>
      </li>
    </ul>
  </section>

  <!-- CONTENT -->
  <section id="content">
    <nav>
      <i class='bx bx-menu'></i>
      <a href="#" class="nav-link">Categories</a>
      <form action="#">
        <div class="form-input">
          <input type="search" placeholder="Search menu..." id="searchInput">
          <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
        </div>
      </form>
      <input type="checkbox" id="switch-mode" hidden>
      <label for="switch-mode" class="switch-mode"></label>
      <a href="#" class="notification">
        <i class='bx bxs-bell'></i>
        <span class="num">8</span>
      </a>
      <a href="#" class="profile">
        <img src="img/people.jpg" alt="Profile">
        <div class="profile-info">
          <h4><?php echo htmlspecialchars($admin_name); ?></h4>
          <p><?php echo htmlspecialchars($admin_email); ?></p>
        </div>
      </a>
    </nav>

    <main>
      <div class="head-title">
        <div class="left"><h1>Menu Management</h1></div>
      </div>

      <div class="controls">
        <div><button class="btn-add" id="openAddModal">+ Add New Item</button></div>
        <div>
          <select id="filterCategory" style="padding:8px;border-radius:8px;border:1px solid #ddd;">
            <option value="">All categories</option>
            <option value="Drinks">Drinks</option>
            <option value="Breads">Breads</option>
          </select>
        </div>
      </div>

      <div class="notice" id="noticeBox"></div>
      <div id="menuGrid" class="menu-grid"></div>
    </main>
  </section>

  <!-- Add Modal -->
  <div id="addModal" style="display:none;" class="modal-backdrop">
    <div class="modal">
      <h3>Add New Item</h3>
      <div class="form-row"><label>Name</label><input type="text" id="add_name" placeholder="Item name"></div>
      <div class="form-row"><label>Category</label>
        <select id="add_category">
          <option value="Drinks">Drinks</option>
          <option value="Breads">Breads</option>
        </select>
      </div>
      <div class="form-row"><label>Price</label><input type="number" id="add_price" placeholder="Price"></div>
      <div class="form-row"><label>Image URL</label><input type="text" id="add_image" placeholder="Image URL"></div>
      <div class="form-row"><label>Description</label><textarea id="add_description" rows="3" placeholder="Short description"></textarea></div>
      <div class="form-actions">
        <button class="btn-cancel" id="addCancel">Cancel</button>
        <button class="btn-save" id="addSave">Save</button>
      </div>
    </div>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" style="display:none;" class="modal-backdrop">
    <div class="modal">
      <h3>Edit Item</h3>
      <input type="hidden" id="edit_id">
      <div class="form-row"><label>Name</label><input type="text" id="edit_name" placeholder="Item name"></div>
      <div class="form-row"><label>Category</label>
        <select id="edit_category">
          <option value="Drinks">Drinks</option>
          <option value="Breads">Breads</option>
        </select>
      </div>
      <div class="form-row"><label>Price</label><input type="number" id="edit_price" placeholder="Price"></div>
      <div class="form-row"><label>Image URL</label><input type="text" id="edit_image" placeholder="Image URL"></div>
      <div class="form-row"><label>Description</label><textarea id="edit_description" rows="3" placeholder="Short description"></textarea></div>
      <div class="form-actions">
        <button class="btn-cancel" id="editCancel">Cancel</button>
        <button class="btn-save" id="editSave">Update</button>
      </div>
    </div>
  </div>

  <script>
    const API_GET = 'api/get_menu_admin.php';
    const API_ADD = 'api/add_menu.php';
    const API_UPDATE = 'api/update_menu.php';
    const API_DELETE = 'api/delete_menu.php';

    const menuGrid = document.getElementById('menuGrid');
    const noticeBox = document.getElementById('noticeBox');
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');

    let menuItems = [];

    function showNotice(msg, success=true) {
      noticeBox.style.display = 'block';
      noticeBox.style.background = success ? '#e6ffed' : '#ffe6e6';
      noticeBox.style.color = success ? '#0a662a' : '#8b0000';
      noticeBox.innerText = msg;
      setTimeout(()=> noticeBox.style.display = 'none', 3000);
    }

    async function loadMenu() {
      try {
        const res = await fetch(API_GET);
        const data = await res.json();
        menuItems = Array.isArray(data) ? data : [];
        renderMenu();
      } catch (err) {
        console.error(err);
        showNotice('Failed to load menu items', false);
      }
    }

            function renderMenu() {
          const category = document.getElementById('filterCategory').value;
          const q = (document.getElementById('searchInput').value || '').toLowerCase();
          menuGrid.innerHTML = '';
          const filtered = menuItems.filter(it => {
            if (category && it.category !== category) return false;
            if (q && !((it.name||'').toLowerCase().includes(q) || (it.description||'').toLowerCase().includes(q))) return false;
            return true;
          });
          if (filtered.length === 0) {
            menuGrid.innerHTML = '<p style="grid-column:1/-1;color:#999;padding:20px;text-align:center;">No items found.</p>';
            return;
          }

          const imgurMap = <?php echo json_encode($imgurUrls); ?>;

          filtered.forEach(item => {
            const imgSrc = imgurMap[item.name] || item.image || '';
            const card = document.createElement('div');
            card.className = 'card';
            card.innerHTML = `
              ${imgSrc ? `<img src="${imgSrc}" alt="${escapeHtml(item.name)}">` : ''}
              <h4>${escapeHtml(item.name)}</h4>
              <div class="meta">₱${Number(item.price).toFixed(2)}</div>
              <div class="cat">${escapeHtml(item.category)}</div>
              <p class="desc">${escapeHtml(item.description)}</p>
              <div class="actions">
                <button class="action-edit" data-id="${item.id}">Edit</button>
                <button class="action-delete" data-id="${item.id}">Delete</button>
              </div>
            `;
            menuGrid.appendChild(card);
          });

          document.querySelectorAll('.action-delete').forEach(btn=>btn.onclick=()=>{if(confirm('Delete this item?')) deleteItem(btn.dataset.id)});
          document.querySelectorAll('.action-edit').forEach(btn=>btn.onclick=()=>openEditModal(btn.dataset.id));
        }


    function escapeHtml(str){if(!str)return '';return String(str).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'","&#039;");}

    document.getElementById('openAddModal').onclick = () => {addModal.style.display='flex';};
    document.getElementById('addCancel').onclick = () => addModal.style.display='none';
    document.getElementById('editCancel').onclick = () => editModal.style.display='none';

    async function deleteItem(id){
      const form = new FormData(); form.append('id', id);
      try{
        const res = await fetch(API_DELETE,{method:'POST', body:form});
        const text = await res.text();
        if(text.trim().toLowerCase().includes('success')){showNotice('Item deleted'); loadMenu();}
        else showNotice('Failed to delete', false);
      }catch(err){console.error(err); showNotice('Failed to delete', false);}
    }

    function openEditModal(id){
      const item = menuItems.find(i=>String(i.id)===String(id));
      if(!item){showNotice('Item not found', false); return;}
      document.getElementById('edit_id').value=item.id;
      document.getElementById('edit_name').value=item.name;
      document.getElementById('edit_category').value=item.category;
      document.getElementById('edit_price').value=item.price;
      document.getElementById('edit_image').value=item.image;
      document.getElementById('edit_description').value=item.description;
      editModal.style.display='flex';
    }

    document.getElementById('addSave').onclick = async ()=>{
      const name=document.getElementById('add_name').value.trim();
      const category=document.getElementById('add_category').value;
      const price=document.getElementById('add_price').value;
      const image=document.getElementById('add_image').value.trim();
      const description=document.getElementById('add_description').value.trim();
      if(!name||!price){alert('Name and price are required'); return;}
      const form=new FormData(); form.append('name',name); form.append('category',category); form.append('price',price); form.append('image',image); form.append('description',description);
      try{const res=await fetch(API_ADD,{method:'POST',body:form}); const text=await res.text();
        if(text.trim().toLowerCase().includes('success')){showNotice('Item added'); addModal.style.display='none'; loadMenu();}
        else{showNotice('Failed to add item', false); console.error('add result:',text);}
      }catch(err){console.error(err); showNotice('Failed to add item', false);}
    };

    document.getElementById('editSave').onclick=async()=>{
      const id=document.getElementById('edit_id').value;
      const name=document.getElementById('edit_name').value.trim();
      const category=document.getElementById('edit_category').value;
      const price=document.getElementById('edit_price').value;
      const image=document.getElementById('edit_image').value.trim();
      const description=document.getElementById('edit_description').value.trim();
      if(!id||!name||!price){alert('ID, name and price are required'); return;}
      const form=new FormData(); form.append('id',id); form.append('name',name); form.append('category',category); form.append('price',price); form.append('image',image); form.append('description',description);
      try{const res=await fetch(API_UPDATE,{method:'POST',body:form}); const text=await res.text();
        if(text.trim().toLowerCase().includes('success')){showNotice('Item updated'); editModal.style.display='none'; loadMenu();}
        else{showNotice('Failed to update', false); console.error('update result:',text);}
      }catch(err){console.error(err); showNotice('Failed to update', false);}
    };

    document.getElementById('filterCategory').onchange = renderMenu;
    document.getElementById('searchInput').oninput = renderMenu;

    loadMenu();
  </script>

</body>
</html>
