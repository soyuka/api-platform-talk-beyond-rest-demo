import fetch from 'isomorphic-unfetch'

let eventSource

const Bookmarks = {
  subscribe: async ({ init, next }) => {
    const res = await fetch(`${process.env.api}/bookmarks`)
    const json = await res.json()

    // js be like
    let hub = res.headers.get('Link')
      .match(/<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/)
    if (hub !== null) { hub = hub[1] }

    init(json['hydra:member'])

    if (!hub) {
      console.warn('No mercure header found')
      return
    }

    const url = new URL(hub)
    url.searchParams.append('topic', `${process.env.api}/bookmarks/{id}`)

    eventSource = new window.EventSource(url)
    eventSource.onmessage = ({ data }) => {
      next({ bookmark: JSON.parse(data) })
    }
  },
  unsubscribe: () => {
    eventSource && eventSource.close()
  },
  remove: async (id) => {
    const res = await fetch(`${process.env.api}/bookmarks/${id}`, { method: 'DELETE' })

    if (res.status !== 204) {
      throw new Error('Failed removing bookmark')
    }
  },
  create: async (link) => {
    const prefix = !link.startsWith('http') ? 'http://' : ''
    link = prefix + link
    const res = await fetch(`${process.env.api}/bookmarks`, {
      method: 'POST',
      body: JSON.stringify({ link }),
      headers: {
        'Content-Type': 'application/ld+json'
      }
    })

    if (res.status === 400) {
      const json = await res.json()
      throw new Error(json.violations[0].message)
    }

    if (res.status === 202) {
      return ''
    }
  },
  search: async (term) => {
    if (!term) {
      return null
    }

    // http://localhost:8080/api/bookmarks/search.jsonld?description=formateur&title=formateur
    const params = new URLSearchParams()
    params.set('title', term)
    params.set('description', term)
    const res = await fetch(`${process.env.api}/bookmarks/search?${params}`)
    const json = await res.json()

    return json['hydra:member']
  }
}

export default Bookmarks
