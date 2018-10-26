const actions = {
	update (context, value) {
		context.commit('UPDATE', value)
	},
	delete (context, value) {
		context.commit('UPDATE', null)
	}
}

export default actions
