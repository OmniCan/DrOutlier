<div class="sidebar">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('admin.dashboard') }}" class="sidebar__main-logo">
                <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="@lang('image')">
            </a>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <!-- Dashboard Menu -->
                <li class="sidebar-menu-item {{ menuActive('admin.dashboard') }}">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link ">
                        <i class="menu-icon las la-chart-line"></i>
                        <span class="menu-title">@lang('Dashboard')</span>
                    </a>
                </li>

                <!-- Users Management -->
                <li class="sidebar__menu-header">@lang('Users Management')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.users.*') }}">
                    <a href="{{ route('admin.users.active') }}" class="nav-link ">
                        <i class="menu-icon las la-user"></i>
                        <span class="menu-title">@lang('All Users')</span>
                        @if ($bannedUsersCount  > 0)
                            <div class="blob white"></div>
                        @endif
                    </a>
                </li>

                <!-- Theory Notes Section -->
                <li class="sidebar__menu-header">@lang('Theory Notes')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.theory-notes-category.index') }}">
                    <a href="{{ route('admin.theory-notes-category.index') }}" class="nav-link ">
                        <i class="menu-icon las la-folder"></i>
                        <span class="menu-title">@lang('Category')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.theory-notes.theory-notes-index') }}">
                    <a href="{{ route('admin.theory-notes.theory-notes-index') }}" class="nav-link ">
                        <i class="menu-icon las la-book"></i>
                        <span class="menu-title">@lang('Notes')</span>
                    </a>
                </li>

                <!-- New Spotters Section -->
                <li class="sidebar__menu-header">@lang('Spotters')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.new-spotters-category.index') }}">
                    <a href="{{ route('admin.new-spotters-category.index') }}" class="nav-link ">
                        <i class="menu-icon las la-folder"></i>
                        <span class="menu-title">@lang('Category')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.new-spotters.new-spotters-index') }}">
                    <a href="{{ route('admin.new-spotters.new-spotters-index') }}" class="nav-link ">
                        <i class="menu-icon las la-eye"></i>
                        <span class="menu-title">@lang('Spotters')</span>
                    </a>
                </li>

                <!-- New OSCE Section -->
                <li class="sidebar__menu-header">@lang('OSCE')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.new-osce-category.index') }}">
                    <a href="{{ route('admin.new-osce-category.index') }}" class="nav-link ">
                        <i class="menu-icon las la-folder"></i>
                        <span class="menu-title">@lang('Category')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.new-osce.new-osce-index') }}">
                    <a href="{{ route('admin.new-osce.new-osce-index') }}" class="nav-link ">
                        <i class="menu-icon las la-stethoscope"></i>
                        <span class="menu-title">@lang('OSCE')</span>
                    </a>
                </li>

                <!-- New Exam Cases Section -->
                <li class="sidebar__menu-header">@lang('Exam Cases')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.new-exam-cases-category.index') }}">
                    <a href="{{ route('admin.new-exam-cases-category.index') }}" class="nav-link ">
                        <i class="menu-icon las la-folder"></i>
                        <span class="menu-title">@lang('Category')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.new-exam-cases.new-exam-cases-index') }}">
                    <a href="{{ route('admin.new-exam-cases.new-exam-cases-index') }}" class="nav-link ">
                        <i class="menu-icon las la-briefcase-medical"></i>
                        <span class="menu-title">@lang('Exam Cases')</span>
                    </a>
                </li>

                <!-- New Table Viva Section -->
                <li class="sidebar__menu-header">@lang('Table Viva')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.new-table-viva-category.index') }}">
                    <a href="{{ route('admin.new-table-viva-category.index') }}" class="nav-link ">
                        <i class="menu-icon las la-folder"></i>
                        <span class="menu-title">@lang('Category')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.new-table-viva.new-table-viva-index') }}">
                    <a href="{{ route('admin.new-table-viva.new-table-viva-index') }}" class="nav-link ">
                        <i class="menu-icon las la-user-md"></i>
                        <span class="menu-title">@lang('Table Viva')</span>
                    </a>
                </li>

                


                <!-- Watch and Learn Section -->
                <li class="sidebar__menu-header">@lang('Watch and Learn')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.watch-and-learn-category.index') }}">
                    <a href="{{ route('admin.watch-and-learn-category.index') }}" class="nav-link ">
                        <i class="menu-icon las la-newspaper"></i>
                        <span class="menu-title">@lang('Category')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.watch-and-learn.watch-index') }}">
                    <a href="{{ route('admin.watch-and-learn.watch-index') }}" class="nav-link ">
                        <i class="menu-icon las la-newspaper"></i>
                        <span class="menu-title">@lang('Watch & Learn')</span>
                    </a>
                </li>

                <!-- Quizora Section -->
                <li class="sidebar__menu-header">@lang('Quizora')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.quiz.category.index') }}">
                    <a href="{{ route('admin.quiz.category.index') }}" class="nav-link ">
                        <i class="menu-icon las la-exchange-alt"></i>
                        <span class="menu-title">@lang('Category')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.quiz.quiz.index') }}">
                    <a href="{{ route('admin.quiz.quiz.index') }}" class="nav-link ">
                        <i class="menu-icon las la-puzzle-piece"></i>

                        <span class="menu-title">@lang('Quiz')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.quiz.index') }}">
                    <a href="{{ route('admin.quiz.index') }}" class="nav-link ">
                        <i class="menu-icon las la-question"></i>
                        <span class="menu-title">@lang('Questions')</span>
                    </a>
                </li>



                <!-- Subscription Management -->
                <li class="sidebar__menu-header">@lang('Subscription Management')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.modules.*') }}">
                    <a href="{{ route('admin.modules.index') }}" class="nav-link">
                        <i class="menu-icon las la-cube"></i>
                        <span class="menu-title">@lang('Modules')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.plans.*') }}">
                    <a href="{{ route('admin.plans.index') }}" class="nav-link">
                        <i class="menu-icon las la-tags"></i>
                        <span class="menu-title">@lang('Plans')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.subscriptions.*') }}">
                    <a href="{{ route('admin.subscriptions.index') }}" class="nav-link">
                        <i class="menu-icon las la-credit-card"></i>
                        <span class="menu-title">@lang('Subscriptions')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.navigation.*') }}">
                    <a href="{{ route('admin.navigation.index') }}" class="nav-link">
                        <i class="menu-icon las la-bars"></i>
                        <span class="menu-title">@lang('Navigation Manager')</span>
                    </a>
                </li>

                <!-- Report Section -->
                <li class="sidebar__menu-header">@lang('Report')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.report.notification.history') }}">
                    <a href="{{ route('admin.report.notification.history') }}" class="nav-link">
                        <i class="menu-icon las la-bell"></i>
                        <span class="menu-title">@lang('Notifications')</span>
                    </a>
                </li>

                <!-- Email Template Section -->
                <li class="sidebar__menu-header">@lang('Email Template')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.email.newsletter.form') }}">
                    <a href="{{ route('admin.email.newsletter.form') }}" class="nav-link">
                        <i class="menu-icon las la-paper-plane"></i>
                        <span class="menu-title">@lang('Send Emails')</span>
                    </a>
                </li>

                <!-- General Settings Section -->
                <li class="sidebar__menu-header">@lang('General Settings')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.setting.index') }}">
                    <a href="{{ route('admin.setting.index') }}" class="nav-link">
                        <i class="menu-icon las la-globe"></i>
                        <span class="menu-title">@lang('Global Settings')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.setting.logo.icon') }}">
                    <a href="{{ route('admin.setting.logo.icon') }}" class="nav-link">
                        <i class="menu-icon las la-image"></i>
                        <span class="menu-title">@lang('Logo & Favicon')</span>
                    </a>
                </li>

                <!-- Notes Section -->
                <!-- <li class="sidebar__menu-header">@lang('Notes')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.category.index') }}">
                    <a href="{{route('admin.category.index')}}" class="nav-link ">
                        <i class="menu-icon las la-exchange-alt"></i>
                        <span class="menu-title">@lang('Note Category')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.blogs.blog-index') }}">
                    <a href="{{ route('admin.blogs.blog-index') }}" class="nav-link ">
                        <i class="menu-icon las la-sticky-note"></i>
                        <span class="menu-title">@lang('Notes')</span>
                    </a>
                </li> -->

                <!-- Spotters Section -->
                <!-- <li class="sidebar__menu-header">@lang('Spotters')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.spotters-category.index') }}">
                    <a href="{{route('admin.spotters-category.index')}}" class="nav-link ">
                        <i class="menu-icon las la-exchange-alt"></i>
                        <span class="menu-title">@lang('Spotter Category')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.spotters.spotters-index') }}">
                    <a href="{{ route('admin.spotters.spotters-index') }}" class="nav-link ">
                        <i class="menu-icon las la-newspaper"></i>
                        <span class="menu-title">@lang('Spotters')</span>
                    </a>
                </li> -->

                <!-- OSCE Section -->
                <!-- <li class="sidebar__menu-header">@lang('OSCE')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.osce-category.index') }}">
                    <a href="{{route('admin.osce-category.index')}}" class="nav-link ">
                        <i class="menu-icon las la-exchange-alt"></i>
                        <span class="menu-title">@lang('OSCE Category')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.osce.osce-index') }}">
                    <a href="{{ route('admin.osce.osce-index') }}" class="nav-link ">
                        <i class="menu-icon las la-newspaper"></i>
                        <span class="menu-title">@lang('OSCE')</span>
                    </a>
                </li> -->

                <!-- Munchies and Nuggets Section -->
                <!-- <li class="sidebar__menu-header">@lang('Munchies and Nuggets')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.munchies-category.index') }}">
                    <a href="{{route('admin.munchies-category.index')}}" class="nav-link ">
                        <i class="menu-icon las la-exchange-alt"></i>
                        <span class="menu-title">@lang('Munchies Category')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.munchies.munchies-index') }}">
                    <a href="{{ route('admin.munchies.munchies-index') }}" class="nav-link ">
                        <i class="menu-icon las la-newspaper"></i>
                        <span class="menu-title">@lang('Munchies')</span>
                    </a>
                </li> -->

                <!-- Back to Basics Section -->
                <!-- <li class="sidebar__menu-header">@lang('Back To Basics')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.basic-category.index') }}">
                    <a href="{{route('admin.basic-category.index')}}" class="nav-link ">
                        <i class="menu-icon las la-exchange-alt"></i>
                        <span class="menu-title">@lang('Basic Category')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.basic.basic-index') }}">
                    <a href="{{ route('admin.basic.basic-index') }}" class="nav-link ">
                        <i class="menu-icon las la-newspaper"></i>
                        <span class="menu-title">@lang('Back To Basics')</span>
                    </a>
                </li> -->

                

                

                <!-- Practical Essentials Section -->
                <!-- <li class="sidebar__menu-header">@lang('Practical Essentials')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.practical-essentials-category.index') }}">
                    <a href="{{ route('admin.practical-essentials-category.index') }}" class="nav-link ">
                        <i class="menu-icon las la-folder"></i>
                        <span class="menu-title">@lang('Practical Essentials Category')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.practical-essentials.practical-essentials-index') }}">
                    <a href="{{ route('admin.practical-essentials.practical-essentials-index') }}" class="nav-link ">
                        <i class="menu-icon las la-tools"></i>
                        <span class="menu-title">@lang('Practical Essentials')</span>
                    </a>
                </li> -->

                <!-- AI Rads Section -->
                <!-- <li class="sidebar__menu-header">@lang('AI Rads')</li>
                <li class="sidebar-menu-item {{ menuActive('admin.ai-rads-category.index') }}">
                    <a href="{{ route('admin.ai-rads-category.index') }}" class="nav-link ">
                        <i class="menu-icon las la-folder"></i>
                        <span class="menu-title">@lang('AI Rads Category')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.ai-rads.ai-rads-index') }}">
                    <a href="{{ route('admin.ai-rads.ai-rads-index') }}" class="nav-link ">
                        <i class="menu-icon las la-brain"></i>
                        <span class="menu-title">@lang('AI Rads')</span>
                    </a>
                </li> -->

                
            </ul>
        </div>
    </div>
</div>

<!-- sidebar end -->

@push('script')
    <script>
        // Scroll the active menu into view
        if ($('li').hasClass('active')) {
            $('#sidebar__menuWrapper').animate({
                scrollTop: eval($(".active").offset().top - 320)
            }, 500);
        }
    </script>
@endpush
