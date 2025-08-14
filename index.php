<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mtwara Gas Plant Maintenance Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- SheetJS library for reading Excel files -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; }
        .modal-overlay { background-color: rgba(0, 0, 0, 0.5); }
        .modal-content { max-height: 80vh; overflow-y: auto; }
        .tab-button.active { background-color: #f3f4f6; color: #1f2937; font-weight: 700; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .action-button {
            transition: all 0.2s ease-in-out;
            transform: scale(1);
        }
        .action-button:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .text-blue-900 { color: #1e3a8a; }
        .bg-blue-900 { background-color: #1e3a8a; }
        .hover\:bg-blue-800:hover { background-color: #1e40af; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- Login Container -->
    <div id="login-container" class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-sm">
            <h2 class="text-3xl font-bold text-center text-blue-900 mb-6">Mtwara Gas Plant</h2>
            <form id="login-form">
                <div class="mb-4">
                    <label for="username" class="block font-semibold mb-1">Username:</label>
                    <input type="text" id="username" name="username" class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="mb-6">
                    <label for="password" class="block font-semibold mb-1">Password:</label>
                    <input type="password" id="password" name="password" class="w-full p-2 border rounded-lg" required>
                </div>
                <button type="submit" class="w-full bg-blue-900 text-white p-2 rounded-lg font-bold hover:bg-blue-800 transition-colors">Log In</button>
                <p id="login-error" class="text-red-500 text-sm text-center mt-4 hidden">Invalid username or password.</p>
            </form>
        </div>
    </div>

    <!-- App Container -->
    <div id="app-container" class="min-h-screen flex flex-col hidden">
        <header class="bg-blue-900 text-white p-6 shadow-lg flex justify-between items-center">
            <h1 class="text-3xl font-bold">Mtwara Gas Plant Maintenance Management System</h1>
            <button onclick="logout()" class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700 transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </button>
        </header>

        <nav class="bg-gray-200 shadow-md">
            <div class="flex flex-wrap justify-center space-x-2 p-2">
                <button id="nav-work-orders" class="tab-button px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-300 transition duration-300 ease-in-out active">
                    <i class="fas fa-tools mr-2"></i>Work Orders
                </button>
                <button id="nav-equipment" class="tab-button px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-300 transition duration-300 ease-in-out">
                    <i class="fas fa-cogs mr-2"></i>Equipment
                </button>
                <button id="nav-inventory" class="tab-button px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-300 transition duration-300 ease-in-out">
                    <i class="fas fa-warehouse mr-2"></i>Inventory
                </button>
                <button id="nav-staff" class="tab-button px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-300 transition duration-300 ease-in-out">
                    <i class="fas fa-users mr-2"></i>Staff Leave
                </button>
                <button id="nav-reports" class="tab-button px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-300 transition duration-300 ease-in-out">
                    <i class="fas fa-chart-line mr-2"></i>Reports
                </button>
            </div>
        </nav>

        <main class="flex-grow p-4 md:p-8">

            <!-- Work Orders Tab -->
            <section id="work-orders-tab" class="tab-content active mx-auto max-w-7xl">
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <h2 class="text-2xl font-bold text-blue-900 mb-4">Work Orders</h2>
                    <div class="flex justify-end space-x-2 mb-4">
                        <button onclick="printWorkOrdersReport()" class="bg-gray-600 text-white px-4 py-2 rounded-lg action-button"><i class="fas fa-print mr-2"></i>Print All Work Orders</button>
                        <button onclick="openModal('addWorkOrderModal')" class="bg-blue-900 text-white px-4 py-2 rounded-lg action-button"><i class="fas fa-plus-circle mr-2"></i>New Work Order</button>
                    </div>
                    <div id="work-order-message" class="hidden mb-4 p-4 text-sm rounded-lg"></div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-200 text-left text-gray-600 uppercase text-sm">
                                    <th class="px-4 py-2 border-b-2 border-gray-300">ID</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Equipment</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Description</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Assigned To</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Status</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Approving Officer</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="work-order-table-body" class="divide-y divide-gray-200">
                                <!-- Work order rows will be rendered here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Equipment Tab -->
            <section id="equipment-tab" class="tab-content mx-auto max-w-7xl">
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <h2 class="text-2xl font-bold text-blue-900 mb-4">Equipment Management</h2>
                    <div class="flex justify-end mb-4">
                        <button onclick="openModal('addEquipmentModal')" class="bg-blue-900 text-white px-4 py-2 rounded-lg action-button"><i class="fas fa-plus-circle mr-2"></i>Add Equipment</button>
                    </div>
                    <div id="equipment-message" class="hidden mb-4 p-4 text-sm rounded-lg"></div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-200 text-left text-gray-600 uppercase text-sm">
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Name</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Running Hours</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Last Maintenance</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Maintenance Type</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="equipment-table-body" class="divide-y divide-gray-200">
                                <!-- Equipment rows will be rendered here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Inventory Tab -->
            <section id="inventory-tab" class="tab-content mx-auto max-w-7xl">
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <h2 class="text-2xl font-bold text-blue-900 mb-4">Stock Inventory</h2>
                    <div class="flex flex-col md:flex-row justify-between items-center mb-4 space-y-2 md:space-y-0">
                        <div class="flex space-x-2 w-full md:w-auto">
                            <button onclick="openModal('addInventoryModal')" class="bg-blue-900 text-white px-4 py-2 rounded-lg action-button"><i class="fas fa-plus-circle mr-2"></i>Add Item</button>
                            <button onclick="printInventoryReport()" class="bg-gray-600 text-white px-4 py-2 rounded-lg action-button"><i class="fas fa-print mr-2"></i>Print Report</button>
                            <button onclick="openModal('printMajorOverhaulModal')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg action-button"><i class="fas fa-print mr-2"></i>Print Major Overhaul Parts</button>
                        </div>
                        <div class="flex items-center w-full md:w-auto">
                            <input type="text" id="inventory-search" placeholder="Search by item name or part number..." class="w-full md:w-64 p-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button onclick="searchInventory()" class="bg-blue-900 text-white px-4 py-2 rounded-r-lg action-button"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <!-- Excel Upload Section -->
                    <div class="mt-4 p-4 border border-gray-300 rounded-lg bg-gray-50 flex flex-col md:flex-row items-center justify-between space-y-2 md:space-y-0 md:space-x-4">
                        <div class="flex-grow w-full md:w-auto">
                            <label for="excel-file-upload" class="block font-semibold mb-1">Upload Spare Parts from Excel:</label>
                            <input type="file" id="excel-file-upload" accept=".xlsx, .xls" class="w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100">
                        </div>
                        <div class="flex space-x-2 w-full md:w-auto">
                            <select id="upload-mode" class="p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="add">Add to stock</option>
                                <option value="replace">Replace stock</option>
                            </select>
                            <button onclick="handleExcelUpload()" class="bg-green-600 text-white px-4 py-2 rounded-lg action-button">
                                <i class="fas fa-upload mr-2"></i>Process Upload
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>Ensure your Excel file has columns named 'name', 'quantity', and 'sparePartNo'.
                    </p>
                    <div id="inventory-message" class="hidden mb-4 p-4 text-sm rounded-lg"></div>
                    <div class="overflow-x-auto mt-4">
                        <table class="min-w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-200 text-left text-gray-600 uppercase text-sm">
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Item</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Quantity</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Spare Part No.</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Status</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inventory-table-body" class="divide-y divide-gray-200">
                                <!-- Inventory rows will be rendered here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Staff Leave Tab -->
            <section id="staff-tab" class="tab-content mx-auto max-w-7xl">
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <h2 class="text-2xl font-bold text-blue-900 mb-4">Staff Leave Management</h2>
                    <div class="flex justify-end mb-4">
                        <button onclick="openModal('addLeaveModal')" class="bg-blue-900 text-white px-4 py-2 rounded-lg action-button"><i class="fas fa-plus-circle mr-2"></i>Add Leave Record</button>
                    </div>
                    <div id="leave-message" class="hidden mb-4 p-4 text-sm rounded-lg"></div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-200 text-left text-gray-600 uppercase text-sm">
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Staff Name</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Start Date</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">End Date</th>
                                    <th class="px-4 py-2 border-b-2 border-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="leave-table-body" class="divide-y divide-gray-200">
                                <!-- Staff leave rows will be rendered here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Reports Tab -->
            <section id="reports-tab" class="tab-content mx-auto max-w-7xl">
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <h2 class="text-2xl font-bold text-blue-900 mb-4">Reports</h2>
                    <div class="flex items-center space-x-4 mb-4">
                        <label for="report-type" class="font-semibold">Select Report Type:</label>
                        <select id="report-type" class="flex-grow p-2 border border-gray-300 rounded-lg">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="annual">Annual</option>
                        </select>
                        <button onclick="generateReport()" class="bg-blue-900 text-white px-4 py-2 rounded-lg action-button"><i class="fas fa-file-alt mr-2"></i>Generate Report</button>
                        <button onclick="printReport()" class="bg-gray-600 text-white px-4 py-2 rounded-lg action-button"><i class="fas fa-print mr-2"></i>Print Report</button>
                    </div>
                    <div id="report-output" class="bg-gray-50 p-4 border border-gray-200 rounded-lg">
                        <p class="text-gray-500">Select a report type and click 'Generate Report' to see the data.</p>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Modals -->

    <!-- Message Modal (replaces alert) -->
    <div id="messageModal" class="modal-overlay hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold" id="messageModalTitle">Message</h3>
                <button class="text-gray-400 hover:text-gray-600" onclick="closeModal('messageModal')"><i class="fas fa-times"></i></button>
            </div>
            <div id="messageModalBody" class="mb-4"></div>
            <div class="flex justify-end space-x-2">
                <button id="messageModalConfirmBtn" class="hidden bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 action-button">Confirm</button>
                <button class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 action-button" onclick="closeModal('messageModal')">Close</button>
            </div>
        </div>
    </div>
    
    <!-- Add Work Order Modal -->
    <div id="addWorkOrderModal" class="modal-overlay hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-2xl modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-blue-900">Add New Work Order</h3>
                <button class="text-gray-400 hover:text-gray-600" onclick="closeModal('addWorkOrderModal')"><i class="fas fa-times"></i></button>
            </div>
            <form id="add-work-order-form">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="wo-equipment" class="block font-semibold mb-1">Equipment:</label>
                        <select id="wo-equipment" name="equipment" class="w-full p-2 border rounded-lg" required></select>
                    </div>
                    <div>
                        <label for="wo-assigned" class="block font-semibold mb-1">Assigned To:</label>
                        <input type="text" id="wo-assigned" name="assignedTo" placeholder="John Doe" class="w-full p-2 border rounded-lg" required>
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label for="wo-description" class="block font-semibold mb-1">Description:</label>
                        <textarea id="wo-description" name="description" rows="3" placeholder="Describe the maintenance task..." class="w-full p-2 border rounded-lg" required></textarea>
                    </div>
                    <div>
                        <label for="wo-maintenance-type" class="block font-semibold mb-1">Maintenance Type:</label>
                        <select id="wo-maintenance-type" name="maintenanceType" class="w-full p-2 border rounded-lg" required>
                            <option value="">Select a type...</option>
                            <option value="major overhaul(E1)">Major Overhaul (E1)</option>
                            <option value="major overhaul(E10)">Major Overhaul (E10)</option>
                            <option value="major overhaul(E20)">Major Overhaul (E20)</option>
                            <option value="major overhaul(E40)">Major Overhaul (E40)</option>
                            <option value="major overhaul(E50)">Major Overhaul (E50)</option>
                            <option value="major overhaul(E60)">Major Overhaul (E60)</option>
                            <option value="major overhaul(E70)">Major Overhaul (E70)</option>
                            <option value="Corrective Maintenance">Corrective Maintenance</option>
                            <option value="Preventive Maintenance">Preventive Maintenance</option>
                            <option value="Breakdown">Breakdown</option>
                        </select>
                    </div>
                    <div>
                        <label for="wo-approving-officer" class="block font-semibold mb-1">Approving Officer:</label>
                        <input type="text" id="wo-approving-officer" name="approvingOfficer" placeholder="Jane Doe" class="w-full p-2 border rounded-lg" required>
                    </div>
                </div>
                <div id="wo-items-container" class="mt-4">
                    <h4 class="font-semibold mb-2">Items to be used:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="wo-item-list">
                        <!-- Inventory items will be populated here -->
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg action-button" onclick="closeModal('addWorkOrderModal')">Cancel</button>
                    <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded-lg action-button">Create Work Order</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Add Equipment Modal -->
    <div id="addEquipmentModal" class="modal-overlay hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-blue-900">Add New Equipment</h3>
                <button class="text-gray-400 hover:text-gray-600" onclick="closeModal('addEquipmentModal')"><i class="fas fa-times"></i></button>
            </div>
            <form id="add-equipment-form">
                <div class="mb-4">
                    <label for="equipment-name" class="block font-semibold mb-1">Equipment Name:</label>
                    <input type="text" id="equipment-name" name="name" class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="equipment-hours" class="block font-semibold mb-1">Running Hours:</label>
                    <input type="number" id="equipment-hours" name="runningHours" class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="equipment-maintenance-type" class="block font-semibold mb-1">Maintenance Type:</label>
                    <select id="equipment-maintenance-type" name="maintenanceType" class="w-full p-2 border rounded-lg">
                        <option value="Preventive Maintenance (PM)">Preventive Maintenance (PM)</option>
                        <option value="Corrective Maintenance">Corrective Maintenance</option>
                        <option value="Predictive Maintenance">Predictive Maintenance</option>
                        <option value="Condition-Based Maintenance (CBM)">Condition-Based Maintenance (CBM)</option>
                        <option value="Scheduled (Time-Based) Maintenance">Scheduled (Time-Based) Maintenance</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg action-button" onclick="closeModal('addEquipmentModal')">Cancel</button>
                    <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded-lg action-button">Add Equipment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Inventory Modal -->
    <div id="addInventoryModal" class="modal-overlay hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-blue-900">Add New Inventory Item</h3>
                <button class="text-gray-400 hover:text-gray-600" onclick="closeModal('addInventoryModal')"><i class="fas fa-times"></i></button>
            </div>
            <form id="add-inventory-form">
                <div class="mb-4">
                    <label for="inventory-name" class="block font-semibold mb-1">Item Name:</label>
                    <input type="text" id="inventory-name" name="name" class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="inventory-quantity" class="block font-semibold mb-1">Quantity:</label>
                    <input type="number" id="inventory-quantity" name="quantity" class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="inventory-spare-part-no" class="block font-semibold mb-1">Spare Part No.:</label>
                    <input type="text" id="inventory-spare-part-no" name="sparePartNo" class="w-full p-2 border rounded-lg">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg action-button" onclick="closeModal('addInventoryModal')">Cancel</button>
                    <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded-lg action-button">Add Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Staff Leave Modal -->
    <div id="addLeaveModal" class="modal-overlay hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-blue-900">Add Staff Leave Record</h3>
                <button class="text-gray-400 hover:text-gray-600" onclick="closeModal('addLeaveModal')"><i class="fas fa-times"></i></button>
            </div>
            <form id="add-leave-form">
                <div class="mb-4">
                    <label for="leave-name" class="block font-semibold mb-1">Staff Name:</label>
                    <input type="text" id="leave-name" name="staffName" class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="leave-start" class="block font-semibold mb-1">Start Date:</label>
                    <input type="date" id="leave-start" name="startDate" class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="leave-end" class="block font-semibold mb-1">End Date:</label>
                    <input type="date" id="leave-end" name="endDate" class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg action-button" onclick="closeModal('addLeaveModal')">Cancel</button>
                    <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded-lg action-button">Add Record</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Print Major Overhaul Report Modal -->
    <div id="printMajorOverhaulModal" class="modal-overlay hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-2xl modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-blue-900">Print Major Overhaul Parts</h3>
                <button class="text-gray-400 hover:text-gray-600" onclick="closeModal('printMajorOverhaulModal')"><i class="fas fa-times"></i></button>
            </div>
            <p class="text-gray-700 mb-4">Select a Major Overhaul Work Order to print the spare parts list.</p>
            <div class="mb-4">
                <label for="major-overhaul-wo-select" class="block font-semibold mb-1">Work Order ID:</label>
                <select id="major-overhaul-wo-select" class="w-full p-2 border rounded-lg"></select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg action-button" onclick="closeModal('printMajorOverhaulModal')">Cancel</button>
                <button type="button" class="bg-blue-900 text-white px-4 py-2 rounded-lg action-button" onclick="printMajorOverhaulReport()">Print Report</button>
            </div>
        </div>
    </div>
    
    <!-- JavaScript for dynamic functionality -->
    <script>
        const showMessage = (message, type = 'info', targetId = 'work-order-message') => {
            const targetEl = document.getElementById(targetId);
            const classes = {
                'success': 'bg-green-100 border-green-400 text-green-700',
                'info': 'bg-blue-100 border-blue-400 text-blue-700',
                'warning': 'bg-yellow-100 border-yellow-400 text-yellow-700',
                'error': 'bg-red-100 border-red-400 text-red-700'
            };
            targetEl.innerHTML = message;
            targetEl.className = `p-4 mb-4 rounded-lg border ${classes[type]}`;
            targetEl.classList.remove('hidden');
            setTimeout(() => targetEl.classList.add('hidden'), 5000);
        };

        const openModal = (modalId) => {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
            if (modalId === 'addWorkOrderModal') {
                populateWorkOrderItems();
            } else if (modalId === 'printMajorOverhaulModal') {
                populateMajorOverhaulWorkOrders();
            }
        };

        const closeModal = (modalId) => {
            const modal = document.getElementById(modalId);
            modal.classList.add('hidden');
        };

        const showView = (tabId) => {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
        };

        const setActiveTab = (buttonId, tabId) => {
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            document.getElementById(buttonId).classList.add('active');
            showView(tabId);
        };

        // --- Data Simulation ---
        let workOrders = [];
        let equipment = [
            { id: 1, name: 'Compressor A', runningHours: 1200, lastMaintenance: '2023-01-15', maintenanceType: 'Preventive Maintenance (PM)' },
            { id: 2, name: 'Pump B', runningHours: 500, lastMaintenance: '2023-04-20', maintenanceType: 'Corrective Maintenance' }
        ];
        let inventory = [
            { id: 1, name: 'Bearing', quantity: 20, initialQuantity: 20, sparePartNo: 'SP-B-123' },
            { id: 2, name: 'Gasket Set', quantity: 15, initialQuantity: 15, sparePartNo: 'SP-G-456' },
            { id: 3, name: 'Filter', quantity: 50, initialQuantity: 50, sparePartNo: 'SP-F-789' },
            { id: 4, name: 'Lubricant Oil', quantity: 12, initialQuantity: 12, sparePartNo: 'SP-LO-101' },
            { id: 5, name: 'Major Overhaul Kit (E1)', quantity: 3, initialQuantity: 3, sparePartNo: 'SP-MO-E1' },
            { id: 6, name: 'Major Overhaul Kit (E20)', quantity: 10, initialQuantity: 10, sparePartNo: 'SP-MO-E20' },
        ];
        let staffLeave = [
            { id: 1, name: 'John Doe', startDate: '2023-06-01', endDate: '2023-06-05' },
            { id: 2, name: 'Jane Smith', startDate: '2023-07-10', endDate: '2023-07-15' }
        ];

        let workOrderIdCounter = 1;
        let inventoryIdCounter = 7;
        let equipmentIdCounter = 3;
        let leaveIdCounter = 3;
        let loggedIn = false;

        // --- Auth Functions ---
        const login = () => {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            // Simple hardcoded login for demonstration
            if (username === 'admin' && password === 'admin') {
                loggedIn = true;
                document.getElementById('login-container').classList.add('hidden');
                document.getElementById('app-container').classList.remove('hidden');
                initializeApp();
            } else {
                document.getElementById('login-error').classList.remove('hidden');
            }
        };

        const logout = () => {
            loggedIn = false;
            document.getElementById('login-container').classList.remove('hidden');
            document.getElementById('app-container').classList.add('hidden');
            document.getElementById('login-form').reset();
            document.getElementById('login-error').classList.add('hidden');
        };

        // --- Rendering Functions ---
        const renderWorkOrders = () => {
            const tbody = document.getElementById('work-order-table-body');
            tbody.innerHTML = '';
            workOrders.forEach(order => {
                const row = document.createElement('tr');
                row.classList.add('hover:bg-gray-50');
                const statusClass = order.status === 'Completed' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800';
                row.innerHTML = `
                    <td class="px-4 py-2 whitespace-nowrap">${order.id}</td>
                    <td class="px-4 py-2 whitespace-nowrap">${order.equipment}</td>
                    <td class="px-4 py-2">${order.description}</td>
                    <td class="px-4 py-2 whitespace-nowrap">${order.assignedTo}</td>
                    <td class="px-4 py-2 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${order.status}</span></td>
                    <td class="px-4 py-2 whitespace-nowrap">${order.approvingOfficer}</td>
                    <td class="px-4 py-2 whitespace-nowrap flex space-x-2">
                        <button onclick="printIndividualWorkOrder(${order.id})" class="text-gray-600 hover:text-gray-900" title="Print Work Order"><i class="fas fa-print"></i></button>
                        <button onclick="completeWorkOrder(${order.id})" class="text-green-600 hover:text-green-900" title="Mark as Complete"><i class="fas fa-check-circle"></i></button>
                        <button onclick="editWorkOrder(${order.id})" class="text-blue-600 hover:text-blue-900" title="Edit"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteWorkOrder(${order.id})" class="text-red-600 hover:text-red-900" title="Delete"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        };

        const renderEquipment = () => {
            const tbody = document.getElementById('equipment-table-body');
            tbody.innerHTML = '';
            equipment.forEach(item => {
                const row = document.createElement('tr');
                row.classList.add('hover:bg-gray-50');
                row.innerHTML = `
                    <td class="px-4 py-2 whitespace-nowrap">${item.name}</td>
                    <td class="px-4 py-2 whitespace-nowrap">${item.runningHours} hrs</td>
                    <td class="px-4 py-2 whitespace-nowrap">${item.lastMaintenance}</td>
                    <td class="px-4 py-2 whitespace-nowrap">${item.maintenanceType}</td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <button onclick="editEquipment(${item.id})" class="text-blue-600 hover:text-blue-900 mr-2"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteEquipment(${item.id})" class="text-red-600 hover:text-red-900"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        };

        const renderInventory = (filteredItems = inventory) => {
            const tbody = document.getElementById('inventory-table-body');
            tbody.innerHTML = '';
            if (filteredItems.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-2 text-center text-gray-500">No items found.</td></tr>`;
                return;
            }
            filteredItems.forEach(item => {
                const row = document.createElement('tr');
                row.classList.add('hover:bg-gray-50');
                const reorderStatus = item.quantity <= item.initialQuantity / 2;
                const statusText = reorderStatus ? `<span class="bg-red-200 text-red-800 px-2 inline-flex text-xs leading-5 font-semibold rounded-full">Re-order</span>` : `<span class="bg-green-200 text-green-800 px-2 inline-flex text-xs leading-5 font-semibold rounded-full">In Stock</span>`;
                row.innerHTML = `
                    <td class="px-4 py-2 whitespace-nowrap">${item.name}</td>
                    <td class="px-4 py-2 whitespace-nowrap">${item.quantity}</td>
                    <td class="px-4 py-2 whitespace-nowrap">${item.sparePartNo || 'N/A'}</td>
                    <td class="px-4 py-2 whitespace-nowrap">${statusText}</td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <button onclick="editInventory(${item.id})" class="text-blue-600 hover:text-blue-900 mr-2"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteInventory(${item.id})" class="text-red-600 hover:text-red-900"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        };

        const renderStaffLeave = () => {
            const tbody = document.getElementById('leave-table-body');
            tbody.innerHTML = '';
            staffLeave.forEach(item => {
                const row = document.createElement('tr');
                row.classList.add('hover:bg-gray-50');
                row.innerHTML = `
                    <td class="px-4 py-2 whitespace-nowrap">${item.name}</td>
                    <td class="px-4 py-2 whitespace-nowrap">${item.startDate}</td>
                    <td class="px-4 py-2 whitespace-nowrap">${item.endDate}</td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <button onclick="editLeave(${item.id})" class="text-blue-600 hover:text-blue-900 mr-2"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteLeave(${item.id})" class="text-red-600 hover:text-red-900"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        };

        // --- Form Population Functions ---
        const populateWorkOrderItems = () => {
            const woItemList = document.getElementById('wo-item-list');
            woItemList.innerHTML = '';
            inventory.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.classList.add('flex', 'items-center', 'space-x-2');
                itemDiv.innerHTML = `
                    <label for="item-${item.id}" class="text-gray-700">${item.name} (${item.quantity} in stock)</label>
                    <input type="number" id="item-${item.id}" data-item-id="${item.id}" min="0" max="${item.quantity}" value="0" class="w-24 p-2 border rounded-lg">
                `;
                woItemList.appendChild(itemDiv);
            });
        };

        const populateMajorOverhaulWorkOrders = () => {
            const select = document.getElementById('major-overhaul-wo-select');
            select.innerHTML = '';
            const majorOverhaulWorkOrders = workOrders.filter(wo => wo.maintenanceType.startsWith('major overhaul'));
            if (majorOverhaulWorkOrders.length === 0) {
                const option = document.createElement('option');
                option.text = "No major overhaul work orders found.";
                option.disabled = true;
                select.add(option);
            } else {
                majorOverhaulWorkOrders.forEach(wo => {
                    const option = document.createElement('option');
                    option.value = wo.id;
                    option.text = `WO-${wo.id}: ${wo.equipment} (${wo.maintenanceType})`;
                    select.add(option);
                });
            }
        };

        // --- Event Listeners and Handlers ---
        document.getElementById('login-form').addEventListener('submit', (e) => {
            e.preventDefault();
            login();
        });

        document.getElementById('add-work-order-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const equipmentName = document.getElementById('wo-equipment').value;
            const assignedTo = document.getElementById('wo-assigned').value;
            const description = document.getElementById('wo-description').value;
            const maintenanceType = document.getElementById('wo-maintenance-type').value;
            const approvingOfficer = document.getElementById('wo-approving-officer').value;

            // Get used inventory items and quantities
            const usedItems = [];
            const itemInputs = document.querySelectorAll('#wo-item-list input[type="number"]');
            let hasInventory = false;
            itemInputs.forEach(input => {
                const quantity = parseInt(input.value);
                if (quantity > 0) {
                    hasInventory = true;
                    const itemId = parseInt(input.getAttribute('data-item-id'));
                    const item = inventory.find(i => i.id === itemId);
                    if (item && item.quantity >= quantity) {
                        item.quantity -= quantity;
                        usedItems.push({ id: itemId, quantity: quantity, name: item.name, sparePartNo: item.sparePartNo });
                    } else {
                        showMessage(`Insufficient stock for ${item.name}. Work order not created.`, 'error');
                        return; // Exit the function to prevent work order creation
                    }
                }
            });

            // Create work order
            const newWorkOrder = {
                id: workOrderIdCounter++,
                equipment: equipmentName,
                assignedTo: assignedTo,
                description: description,
                maintenanceType: maintenanceType,
                approvingOfficer: approvingOfficer,
                status: 'Pending',
                itemsUsed: usedItems
            };
            workOrders.push(newWorkOrder);

            // Re-render the tables
            renderWorkOrders();
            renderInventory();

            closeModal('addWorkOrderModal');
            showMessage('New work order created and inventory updated!', 'success');
            document.getElementById('add-work-order-form').reset();
        });

        document.getElementById('add-equipment-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('equipment-name').value;
            const runningHours = document.getElementById('equipment-hours').value;
            const maintenanceType = document.getElementById('equipment-maintenance-type').value;
            equipment.push({ id: equipmentIdCounter++, name, runningHours, lastMaintenance: new Date().toISOString().slice(0, 10), maintenanceType });
            renderEquipment();
            closeModal('addEquipmentModal');
            showMessage('New equipment added successfully!', 'success', 'equipment-message');
            document.getElementById('add-equipment-form').reset();
        });

        document.getElementById('add-inventory-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('inventory-name').value;
            const quantity = parseInt(document.getElementById('inventory-quantity').value);
            const sparePartNo = document.getElementById('inventory-spare-part-no').value;
            inventory.push({ id: inventoryIdCounter++, name, quantity, initialQuantity: quantity, sparePartNo });
            renderInventory();
            closeModal('addInventoryModal');
            showMessage('New inventory item added successfully!', 'success', 'inventory-message');
            document.getElementById('add-inventory-form').reset();
        });

        document.getElementById('add-leave-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('leave-name').value;
            const startDate = document.getElementById('leave-start').value;
            const endDate = document.getElementById('leave-end').value;
            staffLeave.push({ id: leaveIdCounter++, name, startDate, endDate });
            renderStaffLeave();
            closeModal('addLeaveModal');
            showMessage('New leave record added successfully!', 'success', 'leave-message');
            document.getElementById('add-leave-form').reset();
        });

        document.getElementById('inventory-search').addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filteredItems = inventory.filter(item =>
                item.name.toLowerCase().includes(searchTerm) ||
                (item.sparePartNo && item.sparePartNo.toLowerCase().includes(searchTerm))
            );
            renderInventory(filteredItems);
        });

        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.id.replace('nav-', '') + '-tab';
                setActiveTab(button.id, tabId);
            });
        });

        // --- Action Functions (placeholders) ---
        const completeWorkOrder = (id) => {
            const order = workOrders.find(wo => wo.id === id);
            if (order) {
                order.status = 'Completed';
                renderWorkOrders();
                showMessage(`Work order #${id} marked as completed.`, 'success', 'work-order-message');
            }
        };

        const deleteWorkOrder = (id) => {
            workOrders = workOrders.filter(wo => wo.id !== id);
            renderWorkOrders();
            showMessage(`Work order #${id} deleted.`, 'info', 'work-order-message');
        };

        const deleteEquipment = (id) => {
            equipment = equipment.filter(eq => eq.id !== id);
            renderEquipment();
            showMessage('Equipment deleted.', 'info', 'equipment-message');
        };

        const deleteInventory = (id) => {
            inventory = inventory.filter(inv => inv.id !== id);
            renderInventory();
            showMessage('Inventory item deleted.', 'info', 'inventory-message');
        };

        const deleteLeave = (id) => {
            staffLeave = staffLeave.filter(leave => leave.id !== id);
            renderStaffLeave();
            showMessage('Leave record deleted.', 'info', 'leave-message');
        };

        const editWorkOrder = (id) => {
            // Placeholder for edit functionality
            showMessage(`Editing work order #${id} is not yet implemented.`, 'warning', 'work-order-message');
        };

        const editEquipment = (id) => {
            // Placeholder for edit functionality
            showMessage('Editing equipment is not yet implemented.', 'warning', 'equipment-message');
        };

        const editInventory = (id) => {
            // Placeholder for edit functionality
            showMessage('Editing inventory item is not yet implemented.', 'warning', 'inventory-message');
        };

        const editLeave = (id) => {
            // Placeholder for edit functionality
            showMessage('Editing leave record is not yet implemented.', 'warning', 'leave-message');
        };

        const generateReport = () => {
            const reportType = document.getElementById('report-type').value;
            const reportOutput = document.getElementById('report-output');
            reportOutput.innerHTML = `
                <h3 class="text-xl font-semibold mb-2">Generated ${reportType.charAt(0).toUpperCase() + reportType.slice(1)} Report</h3>
                <p>This is a simulated report for all work orders in a ${reportType} period. In a real system, this would contain detailed data.</p>
                <div class="mt-4">
                    <p>Total Work Orders: ${workOrders.length}</p>
                    <p>Open Work Orders: ${workOrders.filter(wo => wo.status !== 'Completed').length}</p>
                    <p>Completed Work Orders: ${workOrders.filter(wo => wo.status === 'Completed').length}</p>
                </div>
            `;
        };
        
        const printWorkOrdersReport = () => {
            const printWindow = window.open('', '_blank');
            const workOrdersHtml = workOrders.map(order => {
                const statusClass = order.status === 'Completed' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800';
                return `
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px;">${order.id}</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">${order.equipment}</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">${order.description}</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">${order.assignedTo}</td>
                        <td style="border: 1px solid #ccc; padding: 8px;"><span style="padding: 2px 8px; border-radius: 9999px; font-weight: 600;" class="${statusClass}">${order.status}</span></td>
                        <td style="border: 1px solid #ccc; padding: 8px;">${order.approvingOfficer}</td>
                    </tr>
                `;
            }).join('');

            printWindow.document.write(`
                <html>
                <head>
                    <title>Work Orders Report</title>
                    <style>
                        body { font-family: sans-serif; padding: 2rem; }
                        h1 { color: #1e3a8a; }
                        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
                        th, td { text-align: left; padding: 8px; border: 1px solid #ccc; }
                        th { background-color: #f3f4f6; }
                        .bg-green-200 { background-color: #d1fae5; }
                        .text-green-800 { color: #065f46; }
                        .bg-yellow-200 { background-color: #fef9c3; }
                        .text-yellow-800 { color: #854d0e; }
                    </style>
                </head>
                <body>
                    <h1>Work Orders Report</h1>
                    <p>Generated on: ${new Date().toLocaleDateString()}</p>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Equipment</th>
                                <th>Description</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th>Approving Officer</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${workOrdersHtml}
                        </tbody>
                    </table>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        };

        const printIndividualWorkOrder = (id) => {
            const workOrder = workOrders.find(wo => wo.id === id);
            if (!workOrder) {
                showMessage(`Work order #${id} not found.`, 'error', 'work-order-message');
                return;
            }

            const itemsHtml = workOrder.itemsUsed.length > 0 ? workOrder.itemsUsed.map(item => `
                <tr>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item.name}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item.sparePartNo || 'N/A'}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item.quantity}</td>
                </tr>
            `).join('') : '<tr><td colspan="3" style="border: 1px solid #ccc; padding: 8px;">No items used.</td></tr>';

            const printWindow = window.open('', '_blank');
            const statusClass = workOrder.status === 'Completed' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800';

            printWindow.document.write(`
                <html>
                <head>
                    <title>Work Order #${workOrder.id}</title>
                    <style>
                        body { font-family: sans-serif; padding: 2rem; }
                        h1 { color: #1e3a8a; border-bottom: 2px solid #1e3a8a; padding-bottom: 0.5rem; }
                        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem; }
                        .details-grid p { margin: 0; line-height: 1.5; }
                        .details-grid p strong { color: #333; }
                        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-weight: bold; }
                        .bg-green-200 { background-color: #d1fae5; }
                        .text-green-800 { color: #065f46; }
                        .bg-yellow-200 { background-color: #fef9c3; }
                        .text-yellow-800 { color: #854d0e; }
                        .description { margin-top: 1rem; }
                        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
                        th, td { text-align: left; padding: 8px; border: 1px solid #ccc; }
                        th { background-color: #f3f4f6; }
                    </style>
                </head>
                <body>
                    <h1>Work Order #${workOrder.id}</h1>
                    <div class="details-grid">
                        <p><strong>Equipment:</strong> ${workOrder.equipment}</p>
                        <p><strong>Maintenance Type:</strong> ${workOrder.maintenanceType}</p>
                        <p><strong>Assigned To:</strong> ${workOrder.assignedTo}</p>
                        <p><strong>Status:</strong> <span class="status-badge ${statusClass}">${workOrder.status}</span></p>
                        <p><strong>Approving Officer:</strong> ${workOrder.approvingOfficer}</p>
                    </div>
                    <div class="description">
                        <h3>Description:</h3>
                        <p>${workOrder.description}</p>
                    </div>
                    <h3 style="margin-top: 2rem;">Items Used:</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Spare Part No.</th>
                                <th>Quantity Used</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml}
                        </tbody>
                    </table>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        };

        const printReport = () => {
            const reportOutput = document.getElementById('report-output');
            if (reportOutput.innerHTML.includes('Select a report type')) {
                showMessage('Please generate a report first.', 'warning', 'reports-message');
                return;
            }
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>System Report</title>
                    <style>
                        body { font-family: sans-serif; padding: 2rem; }
                        h1 { color: #1e3a8a; }
                        p { margin-bottom: 1rem; }
                    </style>
                </head>
                <body>
                    <h1>System Report</h1>
                    <p>Generated on: ${new Date().toLocaleDateString()}</p>
                    ${reportOutput.innerHTML}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        };

        const printInventoryReport = () => {
            const printWindow = window.open('', '_blank');
            const inventoryHtml = inventory.map(item => `
                <tr>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item.name}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item.quantity}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item.sparePartNo || 'N/A'}</td>
                </tr>
            `).join('');

            printWindow.document.write(`
                <html>
                <head>
                    <title>Inventory Report</title>
                    <style>
                        body { font-family: sans-serif; padding: 2rem; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { text-align: left; padding: 8px; border: 1px solid #ccc; }
                        th { background-color: #f3f4f6; }
                    </style>
                </head>
                <body>
                    <h1>Stock Inventory Report</h1>
                    <p>Generated on: ${new Date().toLocaleDateString()}</p>
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Spare Part No.</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${inventoryHtml}
                        </tbody>
                    </table>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        };
        
        const printMajorOverhaulReport = () => {
            const selectedWoId = document.getElementById('major-overhaul-wo-select').value;
            if (!selectedWoId) {
                showMessage('Please select a Major Overhaul Work Order first.', 'error', 'inventory-message');
                return;
            }

            const workOrder = workOrders.find(wo => wo.id === parseInt(selectedWoId));
            if (!workOrder || workOrder.itemsUsed.length === 0) {
                 showMessage(`No spare parts recorded for Work Order #${selectedWoId}.`, 'error', 'inventory-message');
                 return;
            }

            const printWindow = window.open('', '_blank');
            const itemsHtml = workOrder.itemsUsed.map(item => `
                <tr>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item.name}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item.sparePartNo || 'N/A'}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">${item.quantity}</td>
                </tr>
            `).join('');

            printWindow.document.write(`
                <html>
                <head>
                    <title>Major Overhaul Spare Parts Report</title>
                    <style>
                        body { font-family: sans-serif; padding: 2rem; }
                        h1, h2 { color: #1e3a8a; }
                        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
                        th, td { text-align: left; padding: 8px; border: 1px solid #ccc; }
                        th { background-color: #f3f4f6; }
                    </style>
                </head>
                <body>
                    <h1>Major Overhaul Spare Parts Report</h1>
                    <h2>Work Order #${workOrder.id}</h2>
                    <p><strong>Equipment:</strong> ${workOrder.equipment}</p>
                    <p><strong>Maintenance Type:</strong> ${workOrder.maintenanceType}</p>
                    <p><strong>Assigned To:</strong> ${workOrder.assignedTo}</p>
                    <p><strong>Description:</strong> ${workOrder.description}</p>

                    <h3 style="margin-top: 2rem;">Items Used:</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Spare Part No.</th>
                                <th>Quantity Used</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml}
                        </tbody>
                    </table>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            closeModal('printMajorOverhaulModal');
        };

        // --- Excel Upload Functionality ---
        const handleExcelUpload = () => {
            const fileInput = document.getElementById('excel-file-upload');
            const file = fileInput.files[0];
            const uploadMode = document.getElementById('upload-mode').value;

            if (!file) {
                showMessage('Please select an Excel file to upload.', 'error', 'inventory-message');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                const json = XLSX.utils.sheet_to_json(worksheet);

                // Process the uploaded data
                let newItemsCount = 0;
                let updatedItemsCount = 0;
                const itemsAdded = [];
                const itemsUpdated = [];

                json.forEach(item => {
                    const { name, quantity, sparePartNo } = item;
                    // Check for required fields and validate data
                    if (name && quantity && sparePartNo) {
                        const existingItem = inventory.find(inv => inv.sparePartNo === sparePartNo);
                        if (existingItem) {
                            if (uploadMode === 'add') {
                                existingItem.quantity += parseInt(quantity);
                                itemsUpdated.push({ name: existingItem.name, sparePartNo: existingItem.sparePartNo, oldQuantity: existingItem.quantity - parseInt(quantity), newQuantity: existingItem.quantity });
                            } else if (uploadMode === 'replace') {
                                existingItem.quantity = parseInt(quantity);
                                itemsUpdated.push({ name: existingItem.name, sparePartNo: existingItem.sparePartNo, oldQuantity: 'N/A', newQuantity: existingItem.quantity });
                            }
                            updatedItemsCount++;
                        } else {
                            // Add new item
                            inventory.push({
                                id: inventoryIdCounter++,
                                name: name,
                                quantity: parseInt(quantity),
                                initialQuantity: parseInt(quantity), // Assuming initial quantity is the uploaded quantity for new items
                                sparePartNo: sparePartNo
                            });
                            itemsAdded.push({ name: name, sparePartNo: sparePartNo, quantity: parseInt(quantity) });
                            newItemsCount++;
                        }
                    }
                });

                renderInventory();

                // Generate a detailed success message
                let message = `Successfully processed file.`;
                if (newItemsCount > 0) {
                    message += `<br>Added ${newItemsCount} new item(s): `;
                    message += itemsAdded.map(i => `${i.name} (${i.quantity})`).join(', ');
                }
                if (updatedItemsCount > 0) {
                    message += `<br>Updated ${updatedItemsCount} existing item(s) by ${uploadMode === 'add' ? 'adding to' : 'replacing'} their stock.`;
                }
                
                showMessage(message, 'success', 'inventory-message');
                fileInput.value = ''; // Clear the file input
            };
            reader.onerror = (e) => {
                showMessage('Error reading file. Please try again.', 'error', 'inventory-message');
            };
            reader.readAsArrayBuffer(file);
        };
        
        // --- Initial Data Loading and Setup ---
        const initializeApp = () => {
            renderWorkOrders();
            renderEquipment();
            renderInventory();
            renderStaffLeave();
            populateWorkOrderEquipment();
        };

        const populateWorkOrderEquipment = () => {
            const select = document.getElementById('wo-equipment');
            select.innerHTML = '';
            equipment.forEach(item => {
                const option = document.createElement('option');
                option.value = item.name;
                option.textContent = item.name;
                select.appendChild(option);
            });
        };
        
        document.addEventListener('DOMContentLoaded', () => {
             // Check if user is logged in (for persistence)
             if (loggedIn) {
                 document.getElementById('login-container').classList.add('hidden');
                 document.getElementById('app-container').classList.remove('hidden');
                 initializeApp();
             }
        });
    </script>
</body>
</html>
