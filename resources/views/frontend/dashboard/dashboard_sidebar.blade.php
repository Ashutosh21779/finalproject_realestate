<div class="widget-content">
    <ul class="category-list">
        <li class="{{ Request::is('dashboard') ? 'current' : '' }}">
            <a href="{{ route('dashboard') }}">
                <i class="fa fa-tachometer" aria-hidden="true"></i> Dashboard
            </a>
        </li>

        <li class="{{ Request::is('user/profile') ? 'current' : '' }}">
            <a href="{{ route('user.profile') }}">
                <i class="fa fa-user" aria-hidden="true"></i> Settings
            </a>
        </li>

        <li class="{{ Request::is('user/schedule/request') ? 'current' : '' }}">
            <a href="{{ route('user.schedule.request') }}">
                <i class="fa fa-calendar" aria-hidden="true"></i> Schedule Request
                <span class="badge badge-info">{{ isset($scheduleCount) ? $scheduleCount : '' }}</span>
            </a>
        </li>

        <li class="{{ Request::is('user/compare') ? 'current' : '' }}">
            <a href="{{ route('user.compare') }}">
                <i class="fa fa-balance-scale" aria-hidden="true"></i> Compare
            </a>
        </li>

        <li class="{{ Request::is('user/wishlist') ? 'current' : '' }}">
            <a href="{{ route('user.wishlist') }}">
                <i class="fa fa-heart" aria-hidden="true"></i> WishList
            </a>
        </li>

        <li class="{{ Request::is('user/live/chat') ? 'current' : '' }}">
            <a href="{{ route('user.live.chat') }}">
                <i class="fa fa-comments" aria-hidden="true"></i> Live Chat
            </a>
        </li>

        <li class="{{ Request::is('user/change/password') ? 'current' : '' }}">
            <a href="{{ route('user.change.password') }}">
                <i class="fa fa-lock" aria-hidden="true"></i> Security
            </a>
        </li>

        <li>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 0;">
                @csrf
                <button type="submit" style="background: none; border: none; padding: 0; font: inherit; color: #232323; cursor: pointer; display: flex; align-items: center; width: 100%; text-align: left; padding: 10px 20px;">
                    <i class="fa fa-sign-out" aria-hidden="true" style="margin-right: 8px; font-size: 18px; opacity: 0.8;"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>