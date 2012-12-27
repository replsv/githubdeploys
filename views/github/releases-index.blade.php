<div class="well">
    <h4>
        {{ $project->name }}
        {{ HTML::link('#', 'Deploy', array('class' => 'deploy-btn btn btn-primary',) ) }}
        {{ HTML::link( handles('orchestra::resources/github/manage/' . $project->id), 'Edit', array('class' => 'btn btn-danger') ) }}
    </h4>
    <p>Created on: {{ date('d F Y\, H:i', strtotime($project->created_at)) }}</p>
    <p>Updated on: {{ date('d F Y\, H:i', strtotime($project->updated_at)) }}</p>
    <p>Repository: <strong>{{ $project->repo }}</strong></p>
    <p>Path: <strong>{{ $project->path }}</strong></p>
</div>

<h5>Releases</h5>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Hash</th>
            <th>Date</th>
            <th>Owner</th>
            <th>Options</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($releases as $release)
        <tr>
            <td>#{{ $release->id }}</td>
            <td>
                {{ $release->release }}<br/>
                <small>{{ $project->path }}releases/{{ $release->release }}</small>
            </td>
            <td>{{ date('d-M-Y\, H:i', strtotime($release->added_on)) }}</td>
            <td>{{ $release->users->fullname }}</td>
            <td>
                <div style="float: right;">
                    @if ( $release->release == $currentRelease )
                    {{ HTML::link( '#', 'In production', array('class' => 'btn btn-warning') ) }}
                    @else
                    {{ HTML::link( handles('orchestra::resources/github.releases/use/' . $release->id), 'Use in production', array('class' => 'btn btn-primary') ) }}
                    @endif
                    {{ HTML::link( handles('orchestra::resources/github.releases/view/' . $release->id), 'View', array('class' => 'btn btn-success') ) }}
                    {{ HTML::link( handles('orchestra::resources/github.releases/pull/' . $release->id), 'Pull', array('class' => 'btn btn-info') ) }}
                    {{ HTML::link( '#', 'Delete', array('id' => 'release-' . $release->id, 'class' => 'delete-release-btn btn btn-danger') ) }}
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5">No releases available</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="deploy modal hide fade" tabindex="-1" role="dialog" aria-labelledby="deployModal" aria-hidden="true">
    <form id="deploy" action="{{ URL::to_action('orchestra::resources/github/deploy/' ) }}" method="post">
        <input type="hidden" name="id" id="project-id" value="{{ $project->id }}" />
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Deploy Project</h3>
        </div>
        <div class="modal-body">
            <input type="text" class="input-long" name="branch" id="branch" placeholder="Branch name - Default: master"/>  
        </div>
        <div class="modal-footer">
            <div class="progress progress-warning progress-striped active hide" style="float: left; width: 50%; height: 35px;">
                <div style="width: 50%;" class="bar"></div>
            </div>
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
            <button class="btn btn-primary begin-deploy" type="submit">Begin deploy</button>
        </div>
    </form>
</div>
<div class="delete-release modal hide fade" tabindex="-2" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Delete Release</h3>
    </div>
    <div class="modal-body">
        Are you sure?
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">No</button>
        <a id="delete-href" class="btn btn-success" href="#">Yes</a>
    </div>
</div>
<script type="text/javascript">
    $(".deploy-btn").click(function(event) {
        $("#branch").val('');
        $(".deploy").modal('show');
    });
    $(".begin-deploy").click(function() {
        $(".progress-warning").fadeIn(50);
    });
    $(".delete-release-btn").click(function(event) {
        var releaseId = event.target.id.replace('release-', '');
        if(!$.isNumeric(releaseId)) {
            alert("Something went wrong!");
            return false;
        }
        var url = '{{ str_replace('github/releases', 'github.releases', URL::to_action( 'resources/github.releases@delete/') ) }}/';
        $("#delete-href").attr('href', url + releaseId);
        $(".delete-release").modal('show');
    });
</script>