import { useState } from 'react'
import './App.css'

const btnStyle = {
    padding: '10px',
    background: '#334155',
    color: 'white',
    border: 'none',
    borderRadius: '8px',
    textAlign: 'left',
    cursor: 'pointer',
}

function App() {
    const [menu, setMenu] = useState('dashboard')

    return (
        <div style={{ display: 'flex' }}>
            <aside
                style={{
                    width: '240px',
                    height: '100vh',
                    background: '#1e293b',
                    color: 'white',
                    padding: '20px',
                    position: 'fixed',
                }}
            >
                <h2>Stock Gudang</h2>

                <nav
                    style={{
                        display: 'flex',
                        flexDirection: 'column',
                        gap: '10px',
                        marginTop: '20px',
                    }}
                >
                    <button onClick={() => setMenu('dashboard')} style={btnStyle}>
                        Dashboard
                    </button>

                    <button onClick={() => setMenu('input')} style={btnStyle}>
                        Input Material
                    </button>

                    <button onClick={() => setMenu('output')} style={btnStyle}>
                        Output Material
                    </button>

                    <button onClick={() => setMenu('laporan')} style={btnStyle}>
                        Laporan
                    </button>
                </nav>
            </aside>

            <main style={{ marginLeft: '280px', padding: '30px', width: '100%' }}>
                {menu === 'dashboard' && <h1>Dashboard</h1>}
                {menu === 'input' && <h1>Input Material</h1>}
                {menu === 'output' && <h1>Output Material</h1>}
                {menu === 'laporan' && <Laporan />}
            </main>
        </div>
    )
}

function Laporan() {
    return (
        <div>
            <h1>Laporan Material</h1>
            <p>Ini halaman laporan. Nanti kita sambungkan ke Supabase.</p>

            <table border="1" cellPadding="8" style={{ width: '100%' }}>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Material</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>2026-09-03</td>
                        <td>Minyak Tanah</td>
                        <td>Input</td>
                        <td>50</td>
                    </tr>
                    <tr>
                        <td>2026-09-04</td>
                        <td>Minyak Tanah</td>
                        <td>Output</td>
                        <td>20</td>
                    </tr>
                </tbody>
            </table>
        </div>
    )
}

export default function App() {
    return <h1>Halo Stock Gudang Jalan!</h1>
}