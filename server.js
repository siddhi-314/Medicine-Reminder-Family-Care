const http = require('http');
const fs = require('fs');
const path = require('path');
const { URL } = require('url');

const ROOT = __dirname;
const DATA_DIR = path.join(ROOT, 'data');
const DB_FILE = path.join(DATA_DIR, 'db.json');
const PORT = process.env.PORT || 3000;
if (!fs.existsSync(DATA_DIR)) fs.mkdirSync(DATA_DIR, { recursive: true });
if (!fs.existsSync(DB_FILE)) fs.writeFileSync(DB_FILE, JSON.stringify({
  profile: { name: '', phone: '', email: '', notes: '' },
  family: [], medicines: [], appointments: [], tasks: [], reports: [], history: []
}, null, 2));

function readDB() { return JSON.parse(fs.readFileSync(DB_FILE, 'utf8')); }
function writeDB(db) { fs.writeFileSync(DB_FILE, JSON.stringify(db, null, 2)); }
function send(res, status, data, type='application/json; charset=utf-8') {
  res.writeHead(status, { 'Content-Type': type, 'Access-Control-Allow-Origin': '*', 'Access-Control-Allow-Headers': 'Content-Type', 'Access-Control-Allow-Methods': 'GET,POST,PUT,DELETE,OPTIONS' });
  res.end(type.startsWith('application/json') ? JSON.stringify(data) : data);
}
function body(req) {
  return new Promise((resolve, reject) => {
    let raw=''; req.on('data', chunk => { raw += chunk; if(raw.length > 1e6) req.destroy(); });
    req.on('end', () => { try { resolve(raw ? JSON.parse(raw) : {}); } catch(e) { reject(e); } });
  });
}
const collections = new Set(['family','medicines','appointments','tasks','reports']);
const mime = {'.html':'text/html; charset=utf-8','.css':'text/css; charset=utf-8','.js':'text/javascript; charset=utf-8','.json':'application/json; charset=utf-8','.svg':'image/svg+xml'};
const server = http.createServer(async (req,res) => {
  if (req.method === 'OPTIONS') return send(res, 204, {});
  const u = new URL(req.url, `http://${req.headers.host || 'localhost'}`);
  try {
    if (u.pathname === '/api/health') return send(res,200,{ok:true,backend:'Node.js',storage:'JSON file'});
    if (u.pathname === '/api/profile' && req.method === 'GET') return send(res,200,readDB().profile);
    if (u.pathname === '/api/profile' && (req.method === 'POST' || req.method === 'PUT')) {
      const db=readDB(); db.profile={...db.profile,...await body(req)}; writeDB(db); return send(res,200,db.profile);
    }
    if (u.pathname.startsWith('/api/')) {
      const parts=u.pathname.split('/').filter(Boolean), name=parts[1], id=parts[2];
      if (!collections.has(name)) return send(res,404,{error:'Unknown API resource'});
      const db=readDB();
      if(req.method==='GET') return send(res,200,db[name]);
      if(req.method==='POST') {
        const item=await body(req); item.id=Date.now().toString(36)+Math.random().toString(36).slice(2,7); item.createdAt=new Date().toISOString();
        if(name==='medicines' && item.status && item.status!=='Pending') db.history.unshift({id:item.id+'h',medicineName:item.medicineName,memberName:item.memberName||'',status:item.status,changedAt:new Date().toISOString()});
        db[name].unshift(item); writeDB(db); return send(res,201,item);
      }
      if(id && req.method==='PUT') {
        const item=await body(req), index=db[name].findIndex(x=>x.id===id);
        if(index<0) return send(res,404,{error:'Record not found'});
        const old=db[name][index]; db[name][index]={...old,...item,id};
        if(name==='medicines' && old.status!==db[name][index].status) db.history.unshift({id:id+'-'+Date.now(),medicineName:db[name][index].medicineName,memberName:db[name][index].memberName||'',status:db[name][index].status,changedAt:new Date().toISOString()});
        writeDB(db); return send(res,200,db[name][index]);
      }
      if(id && req.method==='DELETE') {
        const before=db[name].length; db[name]=db[name].filter(x=>x.id!==id);
        if(db[name].length===before) return send(res,404,{error:'Record not found'});
        writeDB(db); return send(res,200,{ok:true});
      }
      return send(res,405,{error:'Method not allowed'});
    }
    if (u.pathname === '/api/history' && req.method==='GET') return send(res,200,readDB().history);
    let requested=decodeURIComponent(u.pathname);
    if(requested==='/' || requested==='/index.html') requested='/index.html';
    const full=path.resolve(ROOT, '.'+requested);
    if(!full.startsWith(ROOT+path.sep) && full!==path.join(ROOT,'index.html')) return send(res,403,'Forbidden','text/plain; charset=utf-8');
    fs.readFile(full,(err,data)=> {
      if(err) return send(res,404,'File not found. Check that the project files were extracted correctly.','text/plain; charset=utf-8');
      send(res,200,data,mime[path.extname(full)]||'application/octet-stream');
    });
  } catch(e) { send(res,400,{error:e.message || 'Request failed'}); }
});
server.listen(PORT,()=>console.log(`Family Care app running at http://localhost:${PORT}`));
