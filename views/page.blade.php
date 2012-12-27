@layout(locate('orchestra::layout.main'))

@section('content')
<div class="row">
    <div class="two columns">
        <h3>{{ $page->title }}</h3>
        {{ $page->content }}
    </div>
</div>
@endsection