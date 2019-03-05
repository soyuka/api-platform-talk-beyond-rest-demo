import Header from './Header'

const Layout = (props) => (
  <div className="container">
    <Header />
    {props.children}
  </div>
)

export default Layout
