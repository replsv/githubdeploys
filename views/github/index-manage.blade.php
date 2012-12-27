<h1>
    @if ($type == 'update' )
    Edit project
    @else
    Add project
    @endif
    {{ HTML::link( handles('orchestra::resources/github'), 'Back', array('class' => 'btn btn-small btn-primary right') ) }}
</h1>
{{ Form::open( handles('orchestra::resources/github/manage/'), 'POST', array('class' => 'awesome')) }}
    <fieldset>
    {{ Form::hidden('id', $project->id) }}
    {{ Form::label('name', 'Project title' ) }}
    {{ Form::text('name', $project->name ) }}
    {{ Form::label('repo', 'Repository' ) }}
    {{ Form::text('repo', $project->repo ) }}
    {{ Form::label('path', 'Deploy path' ) }}
    {{ Form::text('path', $project->path ) }}
    {{ Form::label('submit', '') }}
    {{ Form::submit('Add', array('class' => 'btn btn-large')) }}
    </fieldset>
{{ Form::close() }}