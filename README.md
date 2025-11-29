# Partner Insight 🚀

**Partner Insight** is a comprehensive Distributor Management & Analysis System designed to help businesses monitor performance, evaluate satisfaction, and segment partners using advanced clustering algorithms.

![Dashboard Preview](https://via.placeholder.com/800x400?text=Partner+Insight+Dashboard)

## ✨ Key Features

-   **📊 Interactive Dashboard**: Real-time overview of total distributors, sales volume, satisfaction scores, and cluster distribution.
-   **👥 Distributor Management**: Complete CRUD functionality for managing distributor profiles and details.
-   **📈 Sales Performance Tracking**: Record and monitor sales data (in Cartons) per period.
-   **⭐ Satisfaction Scoring**: Multi-dimensional scoring system including:
    -   Product Quality
    -   Specification Conformity
    -   Consistency
    -   Price vs Quality
    -   Product & Packaging Condition
-   **🧠 K-Means Clustering Analysis**:
    -   Automatically segments distributors into groups (e.g., **Loyal**, **Potential**, **Risky**) based on Sales and Satisfaction data.
    -   Configurable parameters (Number of Clusters $K$, Max Iterations).
    -   Visual Scatter Plots and Distribution Charts.
-   **📂 Data Import/Export**: Seamless integration with Excel for backing up or migrating data.

## 🛠️ Tech Stack

**Backend**
-   **Framework**: Laravel 12.x
-   **Language**: PHP 8.2+
-   **Database**: MySQL / MariaDB
-   **Excel Processing**: Maatwebsite/Excel

**Frontend**
-   **Styling**: Tailwind CSS 4.0
-   **Components**: Flowbite 4.0
-   **Interactivity**: Alpine.js 3.x
-   **Charts**: Chart.js 4.x
-   **Build Tool**: Vite

## ⚙️ Installation

Follow these steps to set up the project locally:

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/partnerInsight.git
cd partnerInsight
```

### 2. Install Dependencies
```bash
# Backend dependencies
composer install

# Frontend dependencies
npm install
```

### 3. Environment Setup
Copy the example environment file and configure your database credentials:
```bash
cp .env.example .env
php artisan key:generate
```
*Edit `.env` and set your `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.*

### 4. Database Migration & Seeding
Run migrations to create tables and seeders to populate initial dummy data:
```bash
php artisan migrate --seed
```

### 5. Build Assets
```bash
npm run build
```

### 6. Run the Application
Start the local development server:
```bash
php artisan serve
```
Access the app at `http://127.0.0.1:8000`.

## 📖 Usage Guide

### Running Clustering Analysis
1.  Navigate to the **Clustering Analysis** menu.
2.  Set the **Number of Clusters (K)** (Default: 3).
3.  Set **Max Iterations** (Default: 100).
4.  Click **Run Analysis**.
5.  View the results in the Scatter Plot and the detailed table below.
6.  Export the results to Excel if needed.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
