<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">{{$pageTitle ?? ''}}</h4>
            @if(!empty($websitePicker))
            <div class="pull-right p-0 m-0">
                @php
                    $websites = config('gsc-cms.websites_domains');
                @endphp
                @if(count($websites) > 1)
                    @if(isset($showRootSiteName) && $showRootSiteName)
                    <a 
                        href="{{ route($routeName, ['activeWebsite' => 'main']) }}" 
                        class="btn waves-effect waves-light mr-2 mb-1 {{/*primer za aktivan i neaktivan sajt*/($activeWebsite == 'main') ? 'btn-success' : 'btn-danger'}}">
                        @if(isset($rootSiteName)) {{ $rootSiteName }} @else Osnovne kategorije @endif
                    </a>
                    @endif
                    @foreach($websites as $website)
                        <a 
                            href="{{ route($routeName, ['activeWebsite' => $website['site_id']]) }}" 
                            class="btn waves-effect waves-light mr-2 mb-1 {{/*primer za aktivan i neaktivan sajt*/($activeWebsite == $website['site_id']) ? 'btn-success' : 'btn-danger'}}">
                            {{$website['short_title']}}
                        </a>
                    @endforeach
                @endif
            </div>
            @elseif(!empty($breadcrumbs))
            <ol class="breadcrumb p-0 m-0">
                @foreach($breadcrumbs as $breadcrumbUrl => $breadcrumbTitle)
                <li>
                    <a href="{{$breadcrumbUrl}}">{{$breadcrumbTitle}}</a>
                </li>
                @endforeach
                <li class="active">{{$pageTitle ?? ''}}</li>
            </ol>
            @endif
            <div class="clearfix"></div>
        </div>
    </div>
</div>