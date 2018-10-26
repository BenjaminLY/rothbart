const actions = {
	updateTokens (context, value) {
		context.commit('UPDATE_TOKENS', value)
	},
	deleteTokens (context, value) {
		context.commit('UPDATE_TOKENS', {
			access_token: null,
			refresh_token: null
		})
	},
	updateNextRoute (context, value) {
		context.commit('UPDATE_NEXT_ROUTE', value)
	},
	deleteNextRoute (context, value) {
		context.commit('UPDATE_NEXT_ROUTE', null)
	}
}

export default actions
