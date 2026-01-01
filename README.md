# 🌳 Family Tree Management System (Laravel)

A web-based **Family Tree Management System** built with **Laravel 12**, allowing users to create, manage, and visualize family relationships in a clean **horizontal tree structure**.

This project supports:
- Parent–child relationships
- Spouse relationships (bidirectional)
- Interactive tree visualization
- PDF export of the family tree
- CRUD operations for persons

---

## ✨ Features

- 👤 Add / Edit / Delete Persons
- 👪 Parent–Child relationships
- 💍 Spouse relationships (husband / wife)
- 🌳 Horizontal family tree view
- 📄 Export family tree as PDF
- 🔍 Search persons by name
- 📑 Pagination support
- 🎨 Gender-based styling
- 🔐 User-based data isolation (`added_by`)

---

## 🛠 Tech Stack

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Blade, Bootstrap 5, CSS
- **Database:** MySQL
- **PDF:** barryvdh/laravel-dompdf
- **Auth:** Laravel Breeze / Jetstream (optional)

---

## 📂 Database Schema

### persons
- id
- name
- dob
- gender (male/female)
- address
- added_by
- root_user

### parent_child
- parent_id
- child_id

### spouses
- person_id
- spouse_id

---

## 🚀 Installation

### 1️⃣ Clone the repository
```bash
git clone https://github.com/karthickraja05/family-tree.git
cd family-tree-laravel







