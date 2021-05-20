<aside class="main-sidebar sidebar-dark-primary elevation-4" style="min-height: 917px;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">{{ trans('panel.site_title') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <form id="tenant-selector" action="{{ route('admin.tenants.active') }}" method="POST">
                        @csrf
                        <input type="hidden" name="next_url" value="{{url()->current()}}">
                        <select class="form-control" name="company_id"
                                onchange="document.getElementById('tenant-selector').submit();">
                            <option value="">All Companies</option>
                            @foreach(tenancy()->getCompanies() as $company)
                                <option {{ tenancy()->activeCompanyIs($company) ? "selected" : "" }} value="{{ $company->id }}">{{ ucfirst($company->name) }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="next_url" value="{{url()->current()}}">
                        <select class="form-control" name="channel_id"
                                onchange="document.getElementById('tenant-selector').submit();">
                            <option value="">All Channels</option>
                            @if(tenancy()->getActiveCompany())
                                @foreach(tenancy()->getActiveCompany()->companyChannels as $tenant)
                                    <option {{ tenancy()->activeTenantIs($tenant) ? "selected" : "" }} value="{{ $tenant->id }}">{{ ucfirst($tenant->name) }}</option>
                                @endforeach
                            @else
                                @foreach(tenancy()->getTenants() as $tenant)
                                    <option {{ tenancy()->activeTenantIs($tenant) ? "selected" : "" }} value="{{ $tenant->id }}">{{ ucfirst($tenant->name) }}</option>
                                @endforeach
                            @endif
                        </select>
                    </form>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route("admin.home") }}">
                        <i class="fas fa-fw fa-tachometer-alt nav-icon">
                        </i>
                        <p>
                            {{ trans('global.dashboard') }}
                        </p>
                    </a>
                </li>
                @can('user_management_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/permissions*") ? "menu-open" : "" }} {{ request()->is("admin/roles*") ? "menu-open" : "" }} {{ request()->is("admin/users*") ? "menu-open" : "" }} {{ request()->is("admin/audit-logs*") ? "menu-open" : "" }} {{ request()->is("admin/user-alerts*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw nav-icon fas fa-users">

                            </i>
                            <p>
                                {{ trans('cruds.userManagement.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('permission_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.permissions.index") }}"
                                       class="nav-link {{ request()->is("admin/permissions") || request()->is("admin/permissions/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-unlock-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.permission.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('role_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.roles.index") }}"
                                       class="nav-link {{ request()->is("admin/roles") || request()->is("admin/roles/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-briefcase">

                                        </i>
                                        <p>
                                            {{ trans('cruds.role.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('user_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.users.index") }}"
                                       class="nav-link {{ request()->is("admin/users") || request()->is("admin/users/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-user">

                                        </i>
                                        <p>
                                            {{ trans('cruds.user.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('audit_log_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.audit-logs.index") }}"
                                       class="nav-link {{ request()->is("admin/audit-logs") || request()->is("admin/audit-logs/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-file-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.auditLog.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('user_alert_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.user-alerts.index") }}"
                                       class="nav-link {{ request()->is("admin/user-alerts") || request()->is("admin/user-alerts/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-bell">

                                        </i>
                                        <p>
                                            {{ trans('cruds.userAlert.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('crm_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/leads*") ? "menu-open" : "" }} {{ request()->is("admin/activities*") ? "menu-open" : "" }} {{ request()->is("admin/activity-comments*") ? "menu-open" : "" }} {{ request()->is("admin/customers*") ? "menu-open" : "" }} {{ request()->is("admin/addresses*") ? "menu-open" : "" }} {{ request()->is("admin/tax-invoices*") ? "menu-open" : "" }} {{ request()->is("admin/notifications*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw nav-icon fas fa-hands-helping">

                            </i>
                            <p>
                                {{ trans('cruds.crm.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('lead_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.leads.index") }}"
                                       class="nav-link {{ request()->is("admin/leads") || request()->is("admin/leads/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-users">

                                        </i>
                                        <p>
                                            {{ trans('cruds.lead.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('activity_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.activities.index") }}"
                                       class="nav-link {{ request()->is("admin/activities") || request()->is("admin/activities/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-comments">

                                        </i>
                                        <p>
                                            {{ trans('cruds.activity.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('activity_comment_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.activity-comments.index") }}"
                                       class="nav-link {{ request()->is("admin/activity-comments") || request()->is("admin/activity-comments/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon far fa-comments">

                                        </i>
                                        <p>
                                            {{ trans('cruds.activityComment.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('customer_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.customers.index") }}"
                                       class="nav-link {{ request()->is("admin/customers") || request()->is("admin/customers/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-user-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.customer.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('address_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.addresses.index") }}"
                                       class="nav-link {{ request()->is("admin/addresses") || request()->is("admin/addresses/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-address-book">

                                        </i>
                                        <p>
                                            {{ trans('cruds.address.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('tax_invoice_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.tax-invoices.index") }}"
                                       class="nav-link {{ request()->is("admin/tax-invoices") || request()->is("admin/tax-invoices/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-file-invoice">

                                        </i>
                                        <p>
                                            {{ trans('cruds.taxInvoice.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('notification_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.notifications.index") }}"
                                       class="nav-link {{ request()->is("admin/notifications") || request()->is("admin/notifications/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-comment-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.notification.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('product_management_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/product-categories*") ? "menu-open" : "" }} {{ request()->is("admin/product-tags*") ? "menu-open" : "" }} {{ request()->is("admin/products*") ? "menu-open" : "" }} {{ request()->is("admin/product-units*") ? "menu-open" : "" }} {{ request()->is("admin/catalogues*") ? "menu-open" : "" }} {{ request()->is("admin/item-product-units*") ? "menu-open" : "" }} {{ request()->is("admin/product-brands*") ? "menu-open" : "" }} {{ request()->is("admin/product-models*") ? "menu-open" : "" }} {{ request()->is("admin/product-versions*") ? "menu-open" : "" }} {{ request()->is("admin/product-category-codes*") ? "menu-open" : "" }} {{ request()->is("admin/coverings*") ? "menu-open" : "" }} {{ request()->is("admin/colours*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw nav-icon fas fa-shopping-cart">

                            </i>
                            <p>
                                {{ trans('cruds.productManagement.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('product_category_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.product-categories.index") }}"
                                       class="nav-link {{ request()->is("admin/product-categories") || request()->is("admin/product-categories/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-tags">

                                        </i>
                                        <p>
                                            {{ trans('cruds.productCategory.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('product_tag_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.product-tags.index") }}"
                                       class="nav-link {{ request()->is("admin/product-tags") || request()->is("admin/product-tags/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-tags">

                                        </i>
                                        <p>
                                            {{ trans('cruds.productTag.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('product_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.products.index") }}"
                                       class="nav-link {{ request()->is("admin/products") || request()->is("admin/products/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-shopping-cart">

                                        </i>
                                        <p>
                                            {{ trans('cruds.product.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('product_unit_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.product-units.index") }}"
                                       class="nav-link {{ request()->is("admin/product-units") || request()->is("admin/product-units/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-shopping-cart">

                                        </i>
                                        <p>
                                            {{ trans('cruds.productUnit.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('catalogue_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.catalogues.index") }}"
                                       class="nav-link {{ request()->is("admin/catalogues") || request()->is("admin/catalogues/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-book">

                                        </i>
                                        <p>
                                            {{ trans('cruds.catalogue.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('item_product_unit_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.item-product-units.index") }}"
                                       class="nav-link {{ request()->is("admin/item-product-units") || request()->is("admin/item-product-units/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-cogs">

                                        </i>
                                        <p>
                                            {{ trans('cruds.itemProductUnit.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('product_brand_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.product-brands.index") }}"
                                       class="nav-link {{ request()->is("admin/product-brands") || request()->is("admin/product-brands/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-tag">

                                        </i>
                                        <p>
                                            {{ trans('cruds.productBrand.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('product_model_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.product-models.index") }}"
                                       class="nav-link {{ request()->is("admin/product-models") || request()->is("admin/product-models/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-tag">

                                        </i>
                                        <p>
                                            {{ trans('cruds.productModel.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('product_version_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.product-versions.index") }}"
                                       class="nav-link {{ request()->is("admin/product-versions") || request()->is("admin/product-versions/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-tag">

                                        </i>
                                        <p>
                                            {{ trans('cruds.productVersion.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('product_category_code_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.product-category-codes.index") }}"
                                       class="nav-link {{ request()->is("admin/product-category-codes") || request()->is("admin/product-category-codes/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-tag">

                                        </i>
                                        <p>
                                            {{ trans('cruds.productCategoryCode.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('covering_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.coverings.index") }}"
                                       class="nav-link {{ request()->is("admin/coverings") || request()->is("admin/coverings/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-gift">

                                        </i>
                                        <p>
                                            {{ trans('cruds.covering.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('colour_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.colours.index") }}"
                                       class="nav-link {{ request()->is("admin/colours") || request()->is("admin/colours/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-palette">

                                        </i>
                                        <p>
                                            {{ trans('cruds.colour.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('marketing_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/discounts*") ? "menu-open" : "" }} {{ request()->is("admin/flash-sales*") ? "menu-open" : "" }} {{ request()->is("admin/promos*") ? "menu-open" : "" }} {{ request()->is("admin/banners*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw nav-icon fas fa-percent">

                            </i>
                            <p>
                                {{ trans('cruds.marketing.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('discount_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.discounts.index") }}"
                                       class="nav-link {{ request()->is("admin/discounts") || request()->is("admin/discounts/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon far fa-money-bill-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.discount.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('promo_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.promos.index") }}"
                                       class="nav-link {{ request()->is("admin/promos") || request()->is("admin/promos/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-gift">

                                        </i>
                                        <p>
                                            {{ trans('cruds.promo.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('banner_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.banners.index") }}"
                                       class="nav-link {{ request()->is("admin/banners") || request()->is("admin/banners/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-flag">

                                        </i>
                                        <p>
                                            {{ trans('cruds.banner.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('corporate_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/companies*") ? "menu-open" : "" }} {{ request()->is("admin/channel-categories*") ? "menu-open" : "" }} {{ request()->is("admin/channels*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw nav-icon fas fa-building">

                            </i>
                            <p>
                                {{ trans('cruds.corporate.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('company_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.companies.index") }}"
                                       class="nav-link {{ request()->is("admin/companies") || request()->is("admin/companies/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon far fa-building">

                                        </i>
                                        <p>
                                            {{ trans('cruds.company.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('channel_category_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.channel-categories.index") }}"
                                       class="nav-link {{ request()->is("admin/channel-categories") || request()->is("admin/channel-categories/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-tags">

                                        </i>
                                        <p>
                                            {{ trans('cruds.channelCategory.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('channel_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.channels.index") }}"
                                       class="nav-link {{ request()->is("admin/channels") || request()->is("admin/channels/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-store-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.channel.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('warehouse_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/items*") ? "menu-open" : "" }} {{ request()->is("admin/shipments*") ? "menu-open" : "" }} {{ request()->is("admin/stocks*") ? "menu-open" : "" }} {{ request()->is("admin/stock-transfers*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw nav-icon fas fa-warehouse">

                            </i>
                            <p>
                                {{ trans('cruds.warehouse.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('item_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.items.index") }}"
                                       class="nav-link {{ request()->is("admin/items") || request()->is("admin/items/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-list">

                                        </i>
                                        <p>
                                            {{ trans('cruds.item.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('shipment_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.shipments.index") }}"
                                       class="nav-link {{ request()->is("admin/shipments") || request()->is("admin/shipments/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-truck">

                                        </i>
                                        <p>
                                            {{ trans('cruds.shipment.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('stock_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.stocks.index") }}"
                                       class="nav-link {{ request()->is("admin/stocks") || request()->is("admin/stocks/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-box-open">

                                        </i>
                                        <p>
                                            {{ trans('cruds.stock.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('stock_transfer_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.stock-transfers.index") }}"
                                       class="nav-link {{ request()->is("admin/stock-transfers") || request()->is("admin/stock-transfers/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-exchange-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.stockTransfer.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('management_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/reports*") ? "menu-open" : "" }} {{ request()->is("admin/targets*") ? "menu-open" : "" }} {{ request()->is("admin/target-schedules*") ? "menu-open" : "" }} {{ request()->is("admin/supervisor-types*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw nav-icon fas fa-cogs">

                            </i>
                            <p>
                                {{ trans('cruds.management.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('report_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.reports.index") }}"
                                       class="nav-link {{ request()->is("admin/reports") || request()->is("admin/reports/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-book">

                                        </i>
                                        <p>
                                            {{ trans('cruds.report.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('target_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.targets.index") }}"
                                       class="nav-link {{ request()->is("admin/targets") || request()->is("admin/targets/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-chart-line">

                                        </i>
                                        <p>
                                            {{ trans('cruds.target.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('target_schedule_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.target-schedules.index") }}"
                                       class="nav-link {{ request()->is("admin/target-schedules") || request()->is("admin/target-schedules/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-calendar-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.targetSchedule.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('supervisor_type_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.supervisor-types.index") }}"
                                       class="nav-link {{ request()->is("admin/supervisor-types") || request()->is("admin/supervisor-types/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-chalkboard-teacher">

                                        </i>
                                        <p>
                                            {{ trans('cruds.supervisorType.title') }}
                                        </p>
                                    </a>
                                </li>
                                @endcan
                        </ul>
                    </li>
                @endcan
                @can('finance_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/orders*") ? "menu-open" : "" }} {{ request()->is("admin/order-details*") ? "menu-open" : "" }} {{ request()->is("admin/order-trackings*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw nav-icon fas fa-dollar-sign">

                            </i>
                            <p>
                                {{ trans('cruds.finance.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('order_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.orders.index") }}"
                                       class="nav-link {{ request()->is("admin/orders") || request()->is("admin/orders/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-cart-arrow-down">

                                        </i>
                                        <p>
                                            {{ trans('cruds.order.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('order_detail_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.order-details.index") }}"
                                       class="nav-link {{ request()->is("admin/order-details") || request()->is("admin/order-details/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-cart-arrow-down">

                                        </i>
                                        <p>
                                            {{ trans('cruds.orderDetail.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('order_tracking_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.order-trackings.index") }}"
                                       class="nav-link {{ request()->is("admin/order-trackings") || request()->is("admin/order-trackings/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-paper-plane">

                                        </i>
                                        <p>
                                            {{ trans('cruds.orderTracking.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('payment_management_access')
                                <li class="nav-item has-treeview {{ request()->is("admin/payment-categories*") ? "menu-open" : "" }} {{ request()->is("admin/payment-types*") ? "menu-open" : "" }} {{ request()->is("admin/payments*") ? "menu-open" : "" }} {{ request()->is("admin/invoices*") ? "menu-open" : "" }}">
                                    <a class="nav-link nav-dropdown-toggle" href="#">
                                        <i class="fa-fw nav-icon fas fa-file-invoice-dollar">

                                        </i>
                                        <p>
                                            {{ trans('cruds.paymentManagement.title') }}
                                            <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @can('payment_category_access')
                                            <li class="nav-item">
                                                <a href="{{ route("admin.payment-categories.index") }}"
                                                   class="nav-link {{ request()->is("admin/payment-categories") || request()->is("admin/payment-categories/*") ? "active" : "" }}">
                                                    <i class="fa-fw nav-icon fas fa-tags">

                                                    </i>
                                                    <p>
                                                        {{ trans('cruds.paymentCategory.title') }}
                                                    </p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('payment_type_access')
                                            <li class="nav-item">
                                                <a href="{{ route("admin.payment-types.index") }}"
                                                   class="nav-link {{ request()->is("admin/payment-types") || request()->is("admin/payment-types/*") ? "active" : "" }}">
                                                    <i class="fa-fw nav-icon fas fa-tags">

                                                    </i>
                                                    <p>
                                                        {{ trans('cruds.paymentType.title') }}
                                                    </p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('payment_access')
                                            <li class="nav-item">
                                                <a href="{{ route("admin.payments.index") }}"
                                                   class="nav-link {{ request()->is("admin/payments") || request()->is("admin/payments/*") ? "active" : "" }}">
                                                    <i class="fa-fw nav-icon fas fa-hand-holding-usd">

                                                    </i>
                                                    <p>
                                                        {{ trans('cruds.payment.title') }}
                                                    </p>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('invoice_access')
                                            <li class="nav-item">
                                                <a href="{{ route("admin.invoices.index") }}"
                                                   class="nav-link {{ request()->is("admin/invoices") || request()->is("admin/invoices/*") ? "active" : "" }}">
                                                    <i class="fa-fw nav-icon fas fa-file-invoice-dollar">

                                                    </i>
                                                    <p>
                                                        {{ trans('cruds.invoice.title') }}
                                                    </p>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @if(tenancy()->getUser()->is_dev)
                    <li class="nav-item has-treeview">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw nav-icon fas fa-cogs">

                            </i>
                            <p>
                                {{ trans('cruds.developer.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url("/admin/log-reader") }}" class="nav-link">
                                    <i class="fa-fw nav-icon fas fa-server">

                                    </i>
                                    <p>Log Reader</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @php($unread = \App\Models\QaMessageUser::unreadCount())
                <li class="nav-item">
                    <a href="{{ route("admin.messenger.index") }}"
                       class="{{ request()->is("admin/messenger") || request()->is("admin/messenger/*") ? "active" : "" }} nav-link">
                        <i class="fa-fw fa fa-envelope nav-icon">

                        </i>
                        <p>{{ trans('global.messages') }}</p>
                        @if($unread > 0)
                            <strong>( {{ $unread }} )</strong>
                        @endif

                    </a>
                </li>
                @can('import_management_access')
                    <li class="nav-item">
                        <a class="nav-link
                                {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}"
                           href="{{ route('admin.import-batches.index') }}">
                            <i class="fa-fw fas fa-file-csv nav-icon">
                            </i>
                            <p>
                                {{ trans('cruds.importBatch.title') }}
                            </p>
                        </a>
                    </li>
                @endcan
                @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
                    @can('profile_password_edit')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}"
                               href="{{ route('profile.password.edit') }}">
                                <i class="fa-fw fas fa-key nav-icon">
                                </i>
                                <p>
                                    {{ trans('global.change_password') }}
                                </p>
                            </a>
                        </li>
                    @endcan
                @endif
                <li class="nav-item">
                    <a href="#" class="nav-link"
                       onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                        <p>
                            <i class="fas fa-fw fa-sign-out-alt nav-icon">

                            </i>
                        <p>{{ trans('global.logout') }}</p>
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
    <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
        <div class="os-scrollbar-track">
            <div class="os-scrollbar-handle"></div>
        </div>
    </div>
    <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-auto-hidden">
        <div class="os-scrollbar-track">
            <div class="os-scrollbar-handle"></div>
        </div>
    </div>
    <div class="os-scrollbar-corner"></div>
</aside>