import Header from './Header'
import Alert from './Alert'

const Layout = ({ error, children, onCreate, onSearch }) => (
  <div className='container'>
    <Alert error={error} />
    <Header onCreate={onCreate} onSearch={onSearch} />
    {children}
  </div>
)

export default Layout
