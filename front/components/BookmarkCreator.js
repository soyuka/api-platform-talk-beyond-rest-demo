import React from 'react'
import fetch from 'isomorphic-unfetch'

class BookmarkCreator extends React.Component {
  constructor(props) {
    super(props)
    this.state = {value: '', error: ''}

    this.handleChange = this.handleChange.bind(this)
    this.handleSubmit = this.handleSubmit.bind(this)
  }

  async handleSubmit(event) {
    event.preventDefault()

    if (!this.state.value.startsWith('http')) {
      this.state.value = 'http://' + this.state.value
    }

    const res = await fetch(`${process.env.api}/bookmarks`, {
      method: 'POST',
      body: JSON.stringify({link: this.state.value}),
      headers: {
        "Content-Type": "application/ld+json"
      }
    })

    if (res.status === 202) {
      this.setState({value: ''})
      return
    }

    if (res.status === 400) {
      const json = await res.json()
      this.setState({error: json.violations[0].message})
    }
  }

  handleChange(event) {
    this.setState({value: event.target.value})
  }

  render() {
    return (
      <form onSubmit={this.handleSubmit}>
        <div className="form-container">
          <input type="text" name="link" value={this.state.value} onChange={this.handleChange} placeholder="https://example.com" />
          <button type="submit">Bookmark</button>
        </div>
        <span className="error">{this.state.error}</span>
      </form>
    )
  }
}

export default BookmarkCreator
