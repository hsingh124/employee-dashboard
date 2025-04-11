const uploadForm = document.getElementById('uploadForm');
const csvFile = document.getElementById('csvFile');
const uploadStatus = document.getElementById('uploadStatus');
const tableBody = document.querySelector('#employeeTable tbody');

function createTextCell(text) {
    const cell = document.createElement('td');
    cell.textContent = text;
    return cell;
}

function createInputCell(emp) {
    const cell = document.createElement('td');
    const input = document.createElement('input');
    input.type = 'text';
    input.value = emp.email_address;
    input.dataset.id = emp.id;
    cell.appendChild(input);
    return cell;
}

function createButtonCell(emp) {
    const cell = document.createElement('td');
    const btn = document.createElement('button');
    btn.textContent = 'Update';
    btn.onclick = () => updateEmail(emp.id, btn);
    cell.appendChild(btn);
    return cell;
}

async function loadEmployees() {
    try {
        const res = await fetch('/employees');
        const data = await res.json();

        tableBody.innerHTML = '';

        data.forEach(emp => {
            const row = document.createElement('tr');
            row.appendChild(createTextCell(emp.id));
            row.appendChild(createTextCell(emp.company_name));
            row.appendChild(createTextCell(emp.employee_name));
            row.appendChild(createInputCell(emp));
            row.appendChild(createTextCell(emp.salary));
            row.appendChild(createButtonCell(emp));
            tableBody.appendChild(row);
        });
    } catch (err) {
        console.error('Failed to load employees:', err);
    }
}

uploadForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData();
    formData.append('csv', csvFile.files[0]);

    try {
        const res = await fetch('/employees/import-from-csv', {
            method: 'POST',
            body: formData
        });

        const result = await res.json();
        uploadStatus.textContent = result.message || result.error;
        uploadStatus.className = 'status ' + (res.ok ? 'success' : 'error');

        if (res.ok) {
            csvFile.value = '';
            loadEmployees();
            loadAverageSalaries();
        }
    } catch (err) {
        uploadStatus.textContent = 'Upload failed';
        uploadStatus.className = 'status error';
    }
});

async function updateEmail(id, button) {
    const input = button.closest('tr').querySelector('input[type="text"]');
    const newEmail = input.value;

    const res = await fetch(`/employees/${id}/email`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            email_address: newEmail
        })
    });

    const result = await res.json();
    alert(result.message || result.error);
}

async function loadAverageSalaries() {
    try {
        const res = await fetch('/companies/average-salaries');
        const data = await res.json();

        const salaryBody = document.querySelector('#salaryTable tbody');
        salaryBody.innerHTML = '';

        data.forEach(company => {
            const row = document.createElement('tr');

            const nameCell = document.createElement('td');
            nameCell.textContent = company.company_name;

            const salaryCell = document.createElement('td');
            salaryCell.textContent = `$${Number(company.average_salary).toLocaleString()}`;

            row.appendChild(nameCell);
            row.appendChild(salaryCell);
            salaryBody.appendChild(row);
        });
    } catch (err) {
        console.error('Failed to load average salaries:', err);
    }
}

loadEmployees();
loadAverageSalaries();
