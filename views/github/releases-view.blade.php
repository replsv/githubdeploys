<div class="well">
    <h4>        
        {{ HTML::link( handles('orchestra::resources/github.releases/index/' . $release->project_id), 'Back to releases list', array('class' => 'btn btn-primary') ) }}
        @if( $currentRelease != $release->release )
        {{ HTML::link( handles('orchestra::resources/github.releases/use/' . $release->id), 'Use in production', array('class' => 'btn btn-danger') ) }}
        @else
        {{ HTML::link( '#', 'In production', array('class' => 'btn btn-warning') ) }}
        @endif
    </h4>
    <p>Hash: {{ $release->release }}</p>
    <p>Created on: {{ date('d F Y\, H:i', strtotime($release->added_on)) }}</p>
    <p>Response: </p>
    <sub>
        {{ implode("<br/>", unserialize($release->response)) }}
    </sub>
</div>