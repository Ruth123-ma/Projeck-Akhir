export default function RootLayout({ children }) {
  return (
    <html>
        <body style={{display: 'flex'}}>
            <nav style={{width: '200px', background: '#1e293b', color: 'white', padding: '20px'}}>
                <h2>Stock Gudang</h2>
                <a href="/">Dashboard</a><br/>
                <a href="/input">Input Material</a><br/>
                <a href="/output">Output Material</a><br/>
                <a href="/stock">Stock Material</a><br/>
                <a href="/laporan">Laporan</a><br/>
            </nav>
            <main style={{padding: '20px', flex: 1}}>{children}</main>
        </body>
    </html>
    )
}