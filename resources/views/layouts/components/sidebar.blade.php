<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar mb-4">
        <a class="sidebar-brand" href="index.html">
            <span class="align-middle">KasKu</span>
        </a>

        <ul class="sidebar-nav">
            @canany(['Dashboard', 'Dashboard Analytics'])

                <li class="sidebar-header">
                    Dashboard
                </li>
                @can('Dashboard')
                    <li class="sidebar-item {{ Route::is(['dashboard']) ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ url('/') }}">
                            <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
                        </a>
                    </li>
                @endcan
            @endcanany
            @canany([
                'User',
                'User Edit',
                'User Create',
                'User Delete',
                'Roles',
                'Roles Edit',
                'Roles Create',
                'Roles
                Delete',
                'Permission',
                'Permission Edit',
                'Permission Create',
                'Permission Delete',
                'Kurs',
                'Kurs Edit',
                'Kurs Create',
                'Kurs Delete',
                'Jurnal Category',
                'Jurnal Category Edit',
                'Jurnal Category Create',
                'Jurnal
                Category Delete',
                ])
                <li class="sidebar-header">
                    Master
                </li>
                @canany(['User', 'User Edit', 'User Create', 'User Delete'])
                    <li class="sidebar-item {{ Route::is(['master.users.*']) ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('master.users.index') }}">
                            <i class="align-middle" data-feather="user"></i> <span class="align-middle">Users</span>
                        </a>
                    </li>
                @endcanany
                @canany(['Roles', 'Roles Edit', 'Roles Create', 'Roles Delete'])
                    <li class="sidebar-item {{ Route::is(['master.roles.*']) ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('master.roles.index') }}">
                            <i class="align-middle" data-feather="slack"></i> <span class="align-middle">Roles</span>
                        </a>
                    </li>
                @endcanany
                @canany(['Permission', 'Permission Edit', 'Permission Create', 'Permission Delete'])
                    <li class="sidebar-item {{ Route::is(['master.permissions.*']) ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('master.permissions.index') }}">
                            <i class="align-middle" data-feather="cpu"></i> <span class="align-middle">Permissions</span>
                        </a>
                    </li>
                @endcanany
                @canany(['Kurs', 'Kurs Edit', 'Kurs Create', 'Kurs Delete'])
                    <li class="sidebar-item {{ Route::is(['master.kurs.*']) ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('master.kurs.index') }}">
                            <i class="align-middle" data-feather="dollar-sign"></i> <span class="align-middle">Kurs</span>
                        </a>
                    </li>
                @endcanany
                @canany(['Jurnal Category', 'Jurnal Category Edit', 'Jurnal Category Create', 'Jurnal Category Delete'])
                    <li class="sidebar-item {{ Route::is(['master.jurnal-category.*']) ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('master.jurnal-category.index') }}">
                            <i class="align-middle" data-feather="trello"></i> <span class="align-middle">Jurnal
                                Category</span>
                        </a>
                    </li>
                @endcanany
            @endcanany
            @canany([
                'Jurnal',
                'Jurnal Edit',
                'Jurnal Create',
                'Jurnal Delete',
                'Jurnal Balance',
                'Jurnal Balance Edit',
                'Jurnal Balance Create',
                'Jurnal Balance Delete',
                'Reimbursement',
                'Reimbursement Edit',
                'Reimbursement
                Create',
                'Reimbursement Delete',
                'Money Changer ',
                'Money Changer Edit',
                'Money Changer Create',
                'Money
                Changer Delete',
                ])
                <li class="sidebar-header">
                    Jurnal
                </li>
                @canany(['Jurnal', 'Jurnal Edit', 'Jurnal Create', 'Jurnal Delete'])
                    <li class="sidebar-item {{ Route::is(['jurnal.*']) ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('jurnal.index') }}">
                            <i class="align-middle" data-feather="book"></i> <span class="align-middle">Jurnal</span>
                        </a>
                    </li>
                @endcanany
                <li class="sidebar-item {{ Route::is(['contracts.*']) ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('contracts.index') }}">
                        <i class="align-middle" data-feather="book-open"></i> <span class="align-middle">Contracts</span>
                    </a>
                </li>
                @canany(['Reimbursement', 'Reimbursement Edit', 'Reimbursement Create', 'Reimbursement Delete'])
                    <li class="sidebar-item {{ Route::is(['reimburses.*']) ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('reimburses.index') }}">
                            <i class="align-middle" data-feather="columns"></i> <span class="align-middle">Reimbursement</span>
                        </a>
                    </li>
                @endcanany
                @canany(['Money Changer ', 'Money Changer Edit', 'Money Changer Create', 'Money Changer Delete'])
                    <li class="sidebar-item {{ Route::is(['money-chargers.*']) ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('money-chargers.index') }}">
                            <i class="align-middle" data-feather="repeat"></i> <span class="align-middle">Money
                                Charger</span>
                        </a>
                    </li>
                @endcanany

            @endcanany



        </ul>


    </div>
</nav>
