<form method="post" action="admin.php?mod=extra-config&amp;plugin=category_access&amp;action=general_submit">
	<div class="row mt-2">
		<div class="col-sm">
			<div class="card">
				<div class="card-header">{l_category_access:legend_general}</div>
				<div class="card-body">
					<table class="table table-hover">
						<tr>
							<td>{l_category_access:label_guest}<br />
								<small>{l_category_access:desc_guest}</small>
							</td>
							<td>{guest_list}</td>
						</tr>
						<tr>
							<td>{l_category_access:label_coment}<br />
								<small>{l_category_access:desc_coment}</small>
							</td>
							<td>{coment_list}</td>
						</tr>
						<tr>
							<td>{l_category_access:label_journ}<br />
								<small>{l_category_access:desc_journ}</small>
							</td>
							<td>{journ_list}</td>
						</tr>
						<tr>
							<td>{l_category_access:label_moder}<br />
								<small>{l_category_access:desc_moder}</small>
							</td>
							<td>{moder_list}</td>
						</tr>
						<tr>
							<td>{l_category_access:label_admin}<br />
								<small>{l_category_access:desc_admin}</small>
							</td>
							<td>{admin_list}</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="card">
				<div class="card-header">{l_category_access:legend_general_text}</b></div>
				<div class="card-body">
					<TEXTAREA NAME="message" COLS="150" ROWS="15">{message}</TEXTAREA>
				</div>
			</div>
		</div>
		<div class="col-sm-12 text-center mt-2">
			<div class="card">
				<div class="card-body">
				<input type="submit" value="{l_category_access:button_save}" class="btn btn-outline-success" />
			</div>
		</div>
		</div>
	</div>
</form>
