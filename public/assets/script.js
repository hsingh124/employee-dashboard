const uploadForm = document.getElementById('uploadForm');
const csvFile = document.getElementById('csvFile');
const uploadStatus = document.getElementById('uploadStatus');
const tableBody = document.querySelector('#employeeTable tbody');

async function loadEmployees() {
    try {
        const res = await fetch('/employees');
        const data = await res.json();

        tableBody.innerHTML = '';

        data.forEach(emp => {
            const row = document.createElement('tr');

            row.innerHTML = `
                <td>${emp.id}</td>
                <td>${emp.company_name}</td>
                <td>${emp.employee_name}</td>
                <td>
                    <input type="text" value="${emp.email_address}" data-id="${emp.id}" />
                </td>
                <td>${emp.salary}</td>
                <td>
                    <button onclick="updateEmail(${emp.id}, this)">Update</button>
                </td>
            `;

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
            row.innerHTML = `
                <td>${company.company_name}</td>
                <td>$${Number(company.average_salary).toLocaleString()}</td>
            `;
            salaryBody.appendChild(row);
        });
    } catch (err) {
        console.error('Failed to load average salaries:', err);
    }
}

loadEmployees();
loadAverageSalaries();
