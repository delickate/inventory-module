<div id="left-sidebar" class="sidebar ">
        <h5 class="brand-name">Inventory</h5>
        <nav id="left-sidebar-nav" class="sidebar-nav">
            <ul class="metismenu">
                <!-- <li class="g_heading">Project</li> -->
                <li class="active"><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>                        
                <!-- <li><a href="project-list.html"><i class="fa fa-list-ol"></i><span>Project list</span></a></li>
                <li><a href="project-taskboard.html"><i class="fa fa-calendar-check-o"></i><span>Taskboard</span></a></li>
                <li><a href="project-ticket.html"><i class="fa fa-list-ul"></i><span>Ticket List</span></a></li>
                <li><a href="project-ticket-details.html"><i class="icon-tag"></i><span>Ticket Details</span></a></li>
                <li><a href="project-clients.html"><i class="fa fa-user"></i><span>Clients</span></a></li>
                <li><a href="project-todo.html"><i class="fa fa-check-square-o"></i><span>Todo List</span></a></li> -->
                <li class="g_heading">Inventory</li>
                
                
                  <li><a class="nav-link" href="<?php echo url('categories/listing'); ?>"><i class="fa fa-support"></i><span>Product categories</span></a></li>
                  <li><a class="nav-link" href="<?php echo url('units/listing'); ?>"><i class="fa fa-support"></i><span>Product Units</span></a></li>
                  <li><a class="nav-link" href="<?php echo url('suppliers/listing'); ?>"><i class="fa fa-support"></i><span>Suppliers</span></a></li>
                  <li><a class="nav-link" href="<?php echo url('warehouses/listing'); ?>"><i class="fa fa-support"></i><span>Warehouses</span></a></li>
                  <li><a class="nav-link" href="<?php echo url('products/listing'); ?>"><i class="fa fa-support"></i><span>Products</span></a></li>
                  <li><a class="nav-link" href="<?php echo url('purchases/listing'); ?>"><i class="fa fa-support"></i><span>Purchase Orders</span></a></li>
                  <!-- <li><a class="nav-link" href="<?php //echo url('purchase_items/listing'); ?>"><i class="fa fa-support"></i><span>Purchases Orders</span></a></li> -->
                  <li><a class="nav-link" href="<?php echo url('inventory/listing'); ?>"><i class="fa fa-support"></i><span>Inventory</span></a></li>
                  <li><a class="nav-link" href="<?php echo url('stock_movements/listing'); ?>"><i class="fa fa-support"></i><span>Stock</span></a></li>
                  
                  
                  
                  <!-- <li><a class="nav-link" href="<?php //echo url('purchase_returns/listing'); ?>"><i class="fa fa-support"></i><span>Purchases returns</span></a></li>
                  <li><a class="nav-link" href="<?php //echo url('purchase_return_items/listing'); ?>"><i class="fa fa-support"></i><span>Purchases returns items</span></a></li> -->
                  <li><a class="nav-link" href="<?php echo url('customers/listing'); ?>"><i class="fa fa-support"></i><span>Customers</span></a></li>
                  <li><a class="nav-link" href="<?php echo url('sales/listing'); ?>"><i class="fa fa-support"></i><span>Sales</span></a></li>
                  <!-- <li><a class="nav-link" href="<?php //echo url('sale_items/listing'); ?>"><i class="fa fa-support"></i><span>Sales items</span></a></li>
                  <li><a class="nav-link" href="<?php //echo url('sale_returns/listing'); ?>"><i class="fa fa-support"></i><span>Sales returns</span></a></li> -->

                <li class="g_heading">Inventory Reports</li>
                <li><a href="{{ url('inventoryReports/CurrentStockStatus') }}"><i class="fa fa-calendar"></i><span>Current Stock Status</span></a></li>
                <li><a href="{{ url('inventoryReports/LowStockAlert') }}"><i class="fa fa-calendar"></i><span>Low Stock Alert</span></a></li>
                <li><a href="{{ url('inventoryReports/SalesSummary') }}"><i class="fa fa-calendar"></i><span>Sales Summary</span></a></li>
                <li><a href="{{ url('inventoryReports/CustomerPurchaseHistory') }}"><i class="fa fa-calendar"></i><span>Customer Purchase History</span></a></li>
                <li><a href="{{ url('inventoryReports/PurchaseSummary') }}"><i class="fa fa-calendar"></i><span>Purchase Summary</span></a></li>
                <!-- <li><a href="{{ url('inventoryReports/ProfitNLoss') }}"><i class="fa fa-calendar"></i><span>Profit & Loss</span></a></li>
                <li><a href="{{ url('inventoryReports/AccountsPayable') }}"><i class="fa fa-calendar"></i><span>Accounts Payable</span></a></li>
                <li><a href="{{ url('inventoryReports/AccountsReceivable') }}"><i class="fa fa-calendar"></i><span>Accounts Receivable</span></a></li> -->


                <li class="g_heading">User management</li>
                <li><a href="{{ url('users/listing') }}"><i class="fa fa-calendar"></i><span>Users</span></a></li>
                <li><a href="{{ url('roles/listing') }}"><i class="fa fa-comments"></i><span>Roles</span></a></li>
                <!-- <li><a href="app-contact.html"><i class="fa fa-address-book"></i><span>Contact</span></a></li>
                <li><a href="app-filemanager.html"><i class="fa fa-folder"></i><span>FileManager</span></a></li>
                <li><a href="app-setting.html"><i class="fa fa-gear"></i><span>Setting</span></a></li>
                <li><a href="page-gallery.html"><i class="fa fa-photo"></i><span>Gallery</span></a></li> -->
               <!--  <li>
                    <a href="javascript:void(0)" class="has-arrow arrow-c"><i class="fa fa-lock"></i><span>Authentication</span></a>
                    <ul>
                        <li><a href="login.html">Login</a></li>
                        <li><a href="register.html">Register</a></li>
                        <li><a href="forgot-password.html">Forgot password</a></li>
                        <li><a href="404.html">404 error</a></li>
                        <li><a href="500.html">500 error</a></li>   
                    </ul>
                </li> -->

            </ul>
        </nav>        
    </div>