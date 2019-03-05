import "../style.css"
import Link from 'next/link'
import fetch from 'isomorphic-unfetch'
import Layout from '../components/Layout.js'
import Bookmark from '../components/Bookmark.js'
import React, { Fragment  } from "react"

const DELIMITER = 5;
const recursivity = bookmarks => {
  if (!bookmarks.length) {
    return null;
  }

  return (
    <Fragment>
      <div className="row">
        {bookmarks.slice(0, DELIMITER).map(bookmark => (
          <Bookmark key={bookmark.id} bookmark={bookmark} />
        ))}
      </div>
      <List bookmarks={bookmarks.slice(DELIMITER)} />
    </Fragment>
  );
};

const List = ({ bookmarks }) => {
  return recursivity(bookmarks);
};

class Index extends React.Component {
  constructor(props) {
    super(props)
    this.state = {bookmarks: props.bookmarks}
  }

  render() {
    return (
      <Layout className="container">
        <List bookmarks={this.props.bookmarks} />
      </Layout>
    )
  }

  componentDidMount() {
		const url = new URL(this.props.hub);
    url.searchParams.append('topic', `${process.env.api}/bookmarks/{id}`);
		const eventSource = new EventSource(url);
    eventSource.onmessage = ({data}) => {
      data = JSON.parse(data)
      const index = this.props.bookmarks.findIndex((e) => e.id === data.id)

      if (!~index) {
        this.state.bookmarks.push(data)
        this.setState({bookmarks: this.state.bookmarks})
        return
      }

      this.state.bookmarks[index] = data
      this.setState({bookmarks: this.state.bookmarks})
    }
  }

  static async getInitialProps() {
    const res = await fetch(`${process.env.api}/bookmarks`)
    const json = await res.json()
		const hub = res.headers.get('Link').match(/<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/)[1]

    return {
      bookmarks: json['hydra:member'],
			hub
    }
  }
}

export default Index
