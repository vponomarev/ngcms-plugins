<h2>{{ lang['x_filter:page_title'] }} <small class="float-right text-muted">{{ count }}</small></h2>

<hr>

<form action="{{ form_action }}" method="post">
    <div class="input-group my-3">
        <input type="search" name="s" value="{{ search }}" class="form-control" placeholder="{{ lang['x_filter:placeholder.search'] }}" />
        <div class="input-group-append">
            <button type="submit" class="xf_btn">&#128269;</button>
        </div>
    </div>
</form>