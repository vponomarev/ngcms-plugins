<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark" style="padding: 20px 0 0 0;">re_stat</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="admin.php"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="admin.php?mod=extras">{l_extras}</a></li>
                <li class="breadcrumb-item active" aria-current="page">re_stat</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.container-fluid -->
<div class="container-fluid">
    <form action="admin.php?mod=extra-config&amp;plugin=re_stat" method="post" name="options_bar">
        <input type="hidden" name="action" value="" />
        <input type="hidden" name="id" value="-1" />
        <div class="row mb-2">
            <div class="col-sm-12 text-center">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <input type="submit" value="Список" class="btn btn-outline-primary"
                        onClick="document.forms['options_bar'].action.value = '';" />
                    <input type="submit" value="Добавить" class="btn btn-outline-primary"
                        onClick="document.forms['options_bar'].action.value = 'add';" />
                </div>
            </div>
        </div>
    </form>
    <form method="post" action="admin.php?mod=extra-config&amp;plugin=re_stat">
        <div class="col-sm-12 mt-2">
            <div class="card">
                <div class="card-header">[add]Добавить[/add][edit]Редакрировать[/edit] элемент списка</div>
                <div class="card-body">
                    <table class="table table-sm table-hover ">
                        <thead>
                            <tr>
                                <td>Код</td>
                                <td><input type="text" size="40" name="code" value="{code}" /></td>
                            </tr>
                            <tr>
                                <td>Статическая страница</td>
                                <td>{statlist}</td>
                            </tr>
                    </table>
                </div>
                <div class="card-footer text-center"><input type="submit"
                        value="[add]Добавить[/add][edit]Редакрировать[/edit] элемент списка"
                        class="btn btn-outline-success" /></div>
                <input type="hidden" name="action" value="confirm" />
                <input type="hidden" name="id" value="{id}" />
            </div>
        </div>
    </form>
</div>
