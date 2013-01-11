<h1>
    Projects
    {{ HTML::link( handles('orchestra::resources/githubdeploys/manage/'), 'Add project', array('class' => 'btn btn-small btn-primary right') ) }}
</h1>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Repo</th>
            <th>Path</th>
            <th>Owner</th>
            <th>Options</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($projects as $project)
        <tr>
            <td>#{{ $project->id }}</td>
            <td>{{ $project->name }}</td>
            <td>{{ $project->repo }}</td>
            <td>{{ $project->path }}</td>
            <td>{{ $project->users->fullname }}</td>
            <td>
                <div style="float: right;">
                    {{ HTML::link( '#', 'Deploy', array('id' => 'project-' . $project->id ,'class' => 'deploy-btn btn btn-primary') ) }}
                    {{ HTML::link( handles('orchestra::resources/githubdeploys/manage/' . $project->id), 'Edit', array('class' => 'btn btn-info') ) }}
                    {{ HTML::link( handles('orchestra::resources/githubdeploys.releases/index/' . $project->id), 'Releases', array('class' => 'btn btn-warning') ) }}
                    {{ HTML::link( '#', 'Delete', array('id' => 'project-' . $project->id, 'class' => 'delete-btn btn btn-danger') ) }}
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6">No projects available</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="deploy modal hide fade" tabindex="-1" role="dialog" aria-labelledby="deployModal" aria-hidden="true">
    <form id="deploy" action="{{ URL::to_action('orchestra::resources/githubdeploys/deploy/') }}" method="post">
        <input type="hidden" name="id" id="project-id" value="" />
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
<div class="delete modal hide fade" tabindex="-2" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Delete Project</h3>
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
        var projectId = event.target.id.replace('project-', '');
        if(!$.isNumeric(projectId)) {
            alert("Something went wrong!");
            return false;
        }
        $("#project-id").val(projectId);
        $("#branch").val('');
        $(".deploy").modal('show');
    });
    $(".begin-deploy").click(function() {
        $(".progress-warning").fadeIn(50);
    });
    $(".delete-btn").click(function(event) {
        var projectId = event.target.id.replace('project-', '');
        if(!$.isNumeric(projectId)) {
            alert("Something went wrong!");
            return false;
        }
        var url = '{{ URL::to_action( 'orchestra::resources/githubdeploys/delete/') }}/';
        $("#delete-href").attr('href', url + projectId);
        $(".delete").modal('show');
    });
</script>