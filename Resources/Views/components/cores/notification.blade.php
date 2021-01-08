@if(config('ladmin.notification', true))
  <li class="nav-item dropdown mr-2">
                
    <a id="navbarDropdown" class="nav-link dropdown-toggle pl-0 ladmin-notification-menu" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
      {!! ladmin()->icon('bell') !!}

      @php
          $count = 0;
      @endphp
      
      @foreach ($notifications as $item)
        @if(auth()->guard(config('ladmin.auth.guard', 'web'))->user()->can($item->gates))
          @php
              $count++;
          @endphp
        @endif
      @endforeach

      @if($count > 0)
        <span class="badge badge-sm badge-danger badge-pill mr-2">&nbsp;</span>
      @endif
    </a>

    <ul class="dropdown-menu shadow-sm mt-3 rounded dropdown-menu-right ladmin-top-menu ladmin-notification-menu-component" aria-labelledby="navbarDropdown">
        <li class="ladmin-top-menu-header text-center">
          <strong>Notifications</strong>
        </li>
        <li class="ladmin-top-menu-body-notification">
          
          <div class="list-unstyled ladmin-notification-item">
            @forelse ($notifications as $item)
            @if(auth()->guard(config('ladmin.auth.guard', 'web'))->user()->can($item->gates))
              <a href="{{ route('administrator.notification.update', [$item->id]) }}" data-link="{{ $item->link }}" class="media my-4 ladmin-notification-link">
                @if(!is_null($item->image_link))
                  <img src="{{ $item->image_link }}" class="mr-3" width="50">
                @endif
                <div class="media-body ladmin-substr">
                  <small class="text-muted float-right">{{ $item->created_at->diffForHumans() }}</small>
                  <strong class="mt-0 mb-1">{{ $item->title }}</strong>
                  <p class="m-0">{!! $item->description !!}</p>
                </div>
              </a>
              @endif
            @empty
                <div class="pt-5 text-center d-flex align-items-center justify-content-center">
                  {!! ladmin()->icon('bell') !!}
                  <div class="text-muted ml-3">No Notification</div>
                </div>
            @endforelse
          </div>

        </li>
        <li class="ladmin-top-menu-footer">
          <a href="{{ route('administrator.notification.index') }}">
            View All
          </a>
        </li>
    </ul>
  </li>
@endif