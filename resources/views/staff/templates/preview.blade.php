@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Template Preview</h1>
                <p class="text-gray-600">Detailed view of the staff hierarchy structure</p>
            </div>
            <a href="{{ route('staff.templates.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                Back to Templates
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-2" id="templateName"></h2>
            <p class="text-gray-600" id="templateDescription"></p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Hierarchy Tree -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Organizational Structure</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div id="hierarchyTree"></div>
                </div>
            </div>

            <!-- Permissions Matrix -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Permissions Overview</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div id="permissionsMatrix"></div>
                </div>
            </div>
        </div>

        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h4 class="font-medium text-blue-900 mb-2">Template Benefits:</h4>
            <ul class="text-sm text-blue-800 space-y-1" id="templateBenefits">
                <!-- Benefits will be populated by JavaScript -->
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const templateType = window.location.pathname.split('/').slice(-2, -1)[0];
    
    fetch(`/staff-templates/${templateType}/preview`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('templateName').textContent = data.name;
            document.getElementById('templateDescription').textContent = data.description;
            
            // Render hierarchy tree
            renderHierarchyTree(data.structure, document.getElementById('hierarchyTree'));
            
            // Render permissions matrix
            renderPermissionsMatrix(data.structure, document.getElementById('permissionsMatrix'));
            
            // Show benefits
            showTemplateBenefits(data.type);
        });
});

function renderHierarchyTree(structure, container, level = 0) {
    for (const [key, node] of Object.entries(structure)) {
        const div = document.createElement('div');
        div.className = 'flex items-center mb-2';
        div.style.marginLeft = `${level * 20}px`;
        
        const roleColor = getRoleColor(key === 'owner' ? 'owner' : node.role);
        
        div.innerHTML = `
            <div class="w-3 h-3 ${roleColor} rounded-full mr-2"></div>
            <span class="text-sm font-medium text-gray-700">
                ${key === 'owner' ? 'Owner' : node.role.charAt(0).toUpperCase() + node.role.slice(1)}
            </span>
        `;
        
        container.appendChild(div);
        
        if (node.children) {
            renderHierarchyTree(node.children, container, level + 1);
        }
    }
}

function renderPermissionsMatrix(structure, container) {
    const permissions = extractAllPermissions(structure);
    const roles = extractAllRoles(structure);
    
    const table = document.createElement('table');
    table.className = 'w-full text-sm';
    
    // Header
    const header = document.createElement('thead');
    header.innerHTML = `
        <tr class="border-b">
            <th class="text-left py-2">Role</th>
            <th class="text-left py-2">Permissions</th>
        </tr>
    `;
    table.appendChild(header);
    
    // Body
    const tbody = document.createElement('tbody');
    roles.forEach(role => {
        const row = document.createElement('tr');
        row.className = 'border-b';
        
        const rolePerms = getRolePermissions(structure, role);
        const permissionBadges = rolePerms.map(perm => 
            `<span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded mr-1 mb-1 inline-block">
                ${perm.replace('_', ' ')}
            </span>`
        ).join('');
        
        row.innerHTML = `
            <td class="py-2 font-medium">${role.charAt(0).toUpperCase() + role.slice(1)}</td>
            <td class="py-2">${permissionBadges}</td>
        `;
        tbody.appendChild(row);
    });
    
    table.appendChild(tbody);
    container.appendChild(table);
}

function extractAllPermissions(structure) {
    const permissions = new Set();
    
    function traverse(node) {
        if (node.permissions) {
            node.permissions.forEach(perm => permissions.add(perm));
        }
        if (node.children) {
            Object.values(node.children).forEach(traverse);
        }
    }
    
    Object.values(structure).forEach(traverse);
    return Array.from(permissions);
}

function extractAllRoles(structure) {
    const roles = new Set();
    
    function traverse(node, key) {
        if (key === 'owner') {
            roles.add('owner');
        } else if (node.role) {
            roles.add(node.role);
        }
        if (node.children) {
            Object.entries(node.children).forEach(([k, n]) => traverse(n, k));
        }
    }
    
    Object.entries(structure).forEach(([k, n]) => traverse(n, k));
    return Array.from(roles);
}

function getRolePermissions(structure, targetRole) {
    function traverse(node, key) {
        if ((key === 'owner' && targetRole === 'owner') || 
            (node.role === targetRole)) {
            return node.permissions || [];
        }
        if (node.children) {
            for (const [k, n] of Object.entries(node.children)) {
                const result = traverse(n, k);
                if (result.length > 0) return result;
            }
        }
        return [];
    }
    
    for (const [k, n] of Object.entries(structure)) {
        const result = traverse(n, k);
        if (result.length > 0) return result;
    }
    return [];
}

function getRoleColor(role) {
    const colors = {
        'owner': 'bg-purple-500',
        'manager': 'bg-blue-500',
        'supervisor': 'bg-green-500',
        'staff': 'bg-gray-400'
    };
    return colors[role] || 'bg-gray-400';
}

function showTemplateBenefits(type) {
    const benefits = {
        'single_property': [
            'Simple 3-tier hierarchy perfect for small properties',
            'Clear reporting structure with minimal complexity',
            'Easy to manage with direct owner oversight',
            'Cost-effective staffing model'
        ],
        'multiple_properties': [
            'Scalable structure for growing businesses',
            'Clear delegation of responsibilities',
            'Supervisor layer for better task management',
            'Suitable for properties with 5+ accommodations'
        ]
    };
    
    const benefitsList = document.getElementById('templateBenefits');
    benefits[type].forEach(benefit => {
        const li = document.createElement('li');
        li.textContent = `• ${benefit}`;
        benefitsList.appendChild(li);
    });
}
</script>
@endsection