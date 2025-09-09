<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Staff Management</title>
  <style>
    body { font-family: Arial;
       background: #333;
        padding: 20px; }
    .container { max-width: 800px;
       margin: auto;
        background: #CDA45E;
         padding: 20px;
          border-radius: 10px; }
    .back-button { display: inline-block;
       margin-bottom: 20px;
        padding: 10px 20px; 
        background: #333;
         color: #fff; 
         text-decoration: none; 
         border-radius: 5px; }
    h2 { text-align: center; }
    .staff-form { display: grid;
       grid-template-columns: repeat(3, 1fr);
        gap: 10px;
         margin-bottom: 20px; }
    .staff-form input, .staff-form button { padding: 10px;
       border-radius: 5px; 
       border: 1px solid #ccc; }
    .staff-form button { grid-column: span 3; 
      background: #333;
       color: white; 
       border: none;
        cursor: pointer; }
    table { width: 100%;
       border-collapse: collapse; }
    th, td { border: 1px solid #ddd; 
      padding: 10px; 
      text-align: center; }
    th { background: #333; 
      color: #fff; }
    .delete-btn { background: #c00; 
      color: white;
       border: none; 
       padding: 6px 10px;
        border-radius: 4px; 
        cursor: pointer; }
  </style>
</head>
<body>
  <div class="container">
    <a href="dashboard_manager.php" class="back-button">← Back to Dashboard</a>
    <h2>Staff Management</h2>

    <form class="staff-form" id="staffForm">
      <input type="text" id="staffName" placeholder="Staff Name" required />
      <input type="text" id="shiftTiming" placeholder="Shift Timing" required />
      <input type="number" id="payroll" placeholder="Payroll (Rs.)" required />
      <button type="submit">Add Staff</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Shift Timing</th>
          <th>Payroll (Rs.)</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="staffTableBody"></tbody>
    </table>
  </div>

  <script>
    const staffForm = document.getElementById("staffForm");
    const staffTableBody = document.getElementById("staffTableBody");

    function loadStaff() {
      fetch('staff_data.php')
        .then(res => res.json())
        .then(data => {
          if(data.success === false) { alert(data.message); return; }
          staffTableBody.innerHTML = '';
          data.forEach(staff => {
            const row = document.createElement('tr');
            row.dataset.id = staff.id;
            row.innerHTML = `
              <td>${staff.name}</td>
              <td>${staff.shift_timing}</td>
              <td>Rs. ${staff.payroll}</td>
              <td><button class="delete-btn">Remove</button></td>
            `;
            staffTableBody.appendChild(row);
          });
        });
    }

    staffForm.addEventListener("submit", e => {
      e.preventDefault();
      const name = document.getElementById("staffName").value.trim();
      const shift = document.getElementById("shiftTiming").value.trim();
      const payroll = document.getElementById("payroll").value.trim();

      fetch('staff_data.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ name, shift_timing: shift, payroll })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          staffForm.reset();
          loadStaff();
        } else {
          alert(data.message || 'Failed to add staff');
        }
      });
    });

    staffTableBody.addEventListener("click", e => {
      if (e.target.classList.contains("delete-btn")) {
        const id = e.target.closest("tr").dataset.id;
        fetch('staff_data.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ action: 'delete', id })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            loadStaff();
          } else {
            alert('Failed to delete staff');
          }
        });
      }
    });

    loadStaff();
  </script>
</body>
</html>
