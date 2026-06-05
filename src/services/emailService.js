const nodemailer = require('nodemailer');

const REQUIRED_EMAIL_ENV = ['GMAIL_USER', 'GMAIL_APP_PASSWORD', 'ADMIN_EMAIL'];

const getMissingEmailConfig = () => REQUIRED_EMAIL_ENV.filter((key) => !process.env[key]);

const isEmailConfigured = () => getMissingEmailConfig().length === 0;

let transporter;
let transporterSignature;

const getTransporter = () => {
  const signature = `${process.env.GMAIL_USER || ''}:${Boolean(process.env.GMAIL_APP_PASSWORD)}`;
  if (transporter && transporterSignature === signature) return transporter;

  if (!isEmailConfigured()) {
    const error = new Error(`Email service is not configured. Missing: ${getMissingEmailConfig().join(', ')}`);
    error.code = 'EMAIL_NOT_CONFIGURED';
    error.statusCode = 503;
    throw error;
  }

  transporter = nodemailer.createTransport({
    service: 'gmail',
    auth: {
      user: process.env.GMAIL_USER,
      pass: process.env.GMAIL_APP_PASSWORD,
    },
  });
  transporterSignature = signature;

  return transporter;
};

const sendMail = async ({ to, subject, text, html }) => {
  const info = await getTransporter().sendMail({
    from: `"HAVOR SMARTA" <${process.env.GMAIL_USER}>`,
    to,
    subject,
    text,
    html,
  });

  return {
    sent: true,
    messageId: info.messageId,
  };
};

const sendMailSafely = async (mailOptions) => {
  try {
    return await sendMail(mailOptions);
  } catch (error) {
    if (process.env.NODE_ENV !== 'production') {
      console.error('Email delivery failed:', error.code || error.message);
    }

    return {
      sent: false,
      reason: error.code === 'EMAIL_NOT_CONFIGURED' ? 'missing_config' : 'delivery_failed',
    };
  }
};

const escapeHtml = (value = '') => String(value)
  .replace(/&/g, '&amp;')
  .replace(/</g, '&lt;')
  .replace(/>/g, '&gt;')
  .replace(/"/g, '&quot;')
  .replace(/'/g, '&#039;');

const isHttpUrl = (value) => /^https?:\/\//i.test(String(value || ''));

const getFrontendUrl = () => String(process.env.FRONTEND_URL || 'https://havorsmarta.netlify.app')
  .trim()
  .replace(/\/+$/, '');

const getLogoUrl = () => {
  const configuredLogoUrl = String(process.env.EMAIL_LOGO_URL || '').trim();
  if (isHttpUrl(configuredLogoUrl)) return configuredLogoUrl;

  return `${getFrontendUrl()}/logo-havor.svg`;
};

const renderButton = (href, label) => {
  if (!isHttpUrl(href)) return '';

  return `
    <a href="${escapeHtml(href)}" style="display:inline-block;background:#1f5dcc;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;line-height:20px;padding:12px 18px;border-radius:999px;margin:8px 8px 0 0;">
      ${escapeHtml(label)}
    </a>
  `;
};

const renderDetailRows = (items) => items
  .filter((item) => item.value !== undefined && item.value !== null && String(item.value).trim() !== '')
  .map((item) => `
    <tr>
      <td style="padding:10px 0;color:#627da6;font-size:13px;width:38%;vertical-align:top;">${escapeHtml(item.label)}</td>
      <td style="padding:10px 0;color:#0e2344;font-size:13px;font-weight:700;vertical-align:top;">${escapeHtml(item.value)}</td>
    </tr>
  `)
  .join('');

const renderEmailCard = ({ title, preview, greeting, body, detailsTitle, details = [], buttons = [], footerNote }) => `
  <!doctype html>
  <html>
    <body style="margin:0;padding:0;background:#f4f8fc;font-family:'Plus Jakarta Sans',Arial,Helvetica,sans-serif;color:#0e2344;">
      <div style="display:none;max-height:0;overflow:hidden;color:transparent;opacity:0;">${escapeHtml(preview)}</div>
      <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f8fc;margin:0;padding:32px 12px;">
        <tr>
          <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #dbe6f4;border-radius:14px;overflow:hidden;">
              <tr>
                <td style="height:5px;background:#1f5dcc;"></td>
              </tr>
              <tr>
                <td style="padding:28px 28px 18px;">
                  <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                      <td style="width:72px;vertical-align:middle;">
                        <img src="${escapeHtml(getLogoUrl())}" width="58" alt="PT Havor Smarta Digital" style="display:block;width:58px;height:auto;border:0;">
                      </td>
                      <td style="vertical-align:middle;">
                        <div style="font-size:20px;font-weight:800;letter-spacing:0;color:#0e2344;">PT Havor Smarta Digital</div>
                        <div style="margin-top:5px;font-size:12px;font-weight:700;text-transform:uppercase;color:#1f5dcc;letter-spacing:0;">Your Digital IT Partner Solution</div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td style="padding:0 28px 24px;">
                  <h1 style="margin:0;font-size:24px;line-height:32px;color:#0e2344;font-weight:800;letter-spacing:0;">${escapeHtml(title)}</h1>
                  <p style="margin:20px 0 0;font-size:15px;line-height:24px;color:#0e2344;">${escapeHtml(greeting)}</p>
                  <div style="margin-top:14px;font-size:15px;line-height:25px;color:#31527d;">${body}</div>
                  ${details.length ? `
                    <div style="margin-top:24px;border:1px solid #dbe6f4;border-radius:10px;padding:16px 18px;background:#f7fbff;">
                      <div style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:0;color:#1f5dcc;">${escapeHtml(detailsTitle || 'Details')}</div>
                      <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:8px;border-collapse:collapse;">
                        ${renderDetailRows(details)}
                      </table>
                    </div>
                  ` : ''}
                  ${buttons.filter(Boolean).length ? `<div style="margin-top:20px;">${buttons.join('')}</div>` : ''}
                  <p style="margin:26px 0 0;font-size:15px;line-height:24px;color:#31527d;">Salam,<br><strong style="color:#0e2344;">PT Havor Smarta Digital</strong></p>
                </td>
              </tr>
              <tr>
                <td style="padding:18px 28px;background:#0e2344;color:#dbeafe;font-size:12px;line-height:20px;">
                  ${escapeHtml(footerNote || 'PT Havor Smarta Digital')}
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </body>
  </html>
`;

const sendCareerEmails = async ({ application, resumeReference }) => {
  const applicantBody = `
    <p style="margin:0 0 12px;">Terima kasih sudah melamar untuk posisi <strong style="color:#0e2344;">${escapeHtml(application.position)}</strong> di PT Havor Smarta Digital.</p>
    <p style="margin:0;">Lamaran kamu sudah kami terima. Tim kami akan meninjau data yang dikirimkan dan akan menghubungi melalui email jika ada informasi lanjutan.</p>
  `;
  const adminBody = `
    <p style="margin:0 0 12px;">Ada lamaran baru yang masuk melalui website PT Havor Smarta Digital.</p>
    <p style="margin:0;">Silakan cek detail pelamar dan resume melalui dashboard admin.</p>
  `;
  const resumeButton = renderButton(resumeReference, 'View Resume');
  const portfolioButton = renderButton(application.portfolioUrl, 'View Portfolio');

  const userEmail = await sendMailSafely({
    to: application.email,
    subject: 'Lamaran Berhasil Dikirim - PT Havor Smarta Digital',
    text: [
      `Hi ${application.fullName},`,
      '',
      `Terima kasih sudah melamar untuk posisi ${application.position} di PT Havor Smarta Digital.`,
      '',
      'Lamaran kamu sudah kami terima. Tim kami akan meninjau data yang dikirimkan dan akan menghubungi melalui email jika ada informasi lanjutan.',
      '',
      'Salam,',
      'PT Havor Smarta Digital',
    ].join('\n'),
    html: renderEmailCard({
      title: 'Lamaran Berhasil Dikirim',
      preview: `Lamaran untuk posisi ${application.position} sudah diterima.`,
      greeting: `Hi ${application.fullName},`,
      body: applicantBody,
      detailsTitle: 'Ringkasan Lamaran',
      details: [
        { label: 'Posisi', value: application.position },
        { label: 'Email', value: application.email },
      ],
      footerNote: 'PT Havor Smarta Digital - Your Digital IT Partner Solution',
    }),
  });

  const adminEmail = await sendMailSafely({
    to: process.env.ADMIN_EMAIL,
    subject: `New Career Application - ${application.position} - ${application.fullName}`,
    text: [
      'Ada lamaran baru yang masuk melalui website PT Havor Smarta Digital.',
      '',
      `Name: ${application.fullName}`,
      `Email: ${application.email}`,
      `Phone: ${application.phone}`,
      `Address: ${application.address || '-'}`,
      `Position: ${application.position}`,
      `Latest Education: ${application.latestEducation || '-'}`,
      `Experience: ${application.experienceSummary || '-'}`,
      `Portfolio: ${application.portfolioUrl || '-'}`,
      '',
      'Message/Cover Letter:',
      application.message,
      '',
      'Resume:',
      resumeReference || '-',
    ].join('\n'),
    html: renderEmailCard({
      title: 'New Career Application',
      preview: `${application.fullName} applied for ${application.position}.`,
      greeting: 'Hi Admin PT Havor Smarta Digital,',
      body: adminBody,
      detailsTitle: 'Applicant Details',
      details: [
        { label: 'Name', value: application.fullName },
        { label: 'Email', value: application.email },
        { label: 'Phone', value: application.phone },
        { label: 'Address', value: application.address || '-' },
        { label: 'Position', value: application.position },
        { label: 'Latest Education', value: application.latestEducation || '-' },
        { label: 'Experience', value: application.experienceSummary || '-' },
        { label: 'Portfolio', value: application.portfolioUrl || '-' },
        { label: 'Resume Reference', value: isHttpUrl(resumeReference) ? 'Signed resume link available' : (resumeReference || '-') },
      ],
      buttons: [resumeButton, portfolioButton],
      footerNote: 'Notifikasi ini dikirim dari website PT Havor Smarta Digital.',
    }),
  });

  return { userEmail, adminEmail };
};

const sendContactEmails = async ({ message }) => {
  const senderBody = `
    <p style="margin:0 0 12px;">Terima kasih sudah menghubungi PT Havor Smarta Digital.</p>
    <p style="margin:0;">Pesan kamu sudah kami terima. Tim kami akan meninjau pesan tersebut dan membalas melalui email secepatnya.</p>
  `;
  const adminBody = `
    <p style="margin:0;">Ada pesan baru yang dikirim melalui website PT Havor Smarta Digital.</p>
  `;

  const userEmail = await sendMailSafely({
    to: message.email,
    subject: 'Pesan Berhasil Dikirim - PT Havor Smarta Digital',
    text: [
      `Hi ${message.name},`,
      '',
      'Terima kasih sudah menghubungi PT Havor Smarta Digital.',
      '',
      'Pesan kamu sudah kami terima. Tim kami akan meninjau pesan tersebut dan membalas melalui email secepatnya.',
      '',
      'Salam,',
      'PT Havor Smarta Digital',
    ].join('\n'),
    html: renderEmailCard({
      title: 'Pesan Berhasil Dikirim',
      preview: 'Pesan kamu sudah diterima oleh PT Havor Smarta Digital.',
      greeting: `Hi ${message.name},`,
      body: senderBody,
      detailsTitle: 'Ringkasan Pesan',
      details: [
        { label: 'Subjek', value: message.subject },
        { label: 'Email', value: message.email },
      ],
      buttons: [renderButton(getFrontendUrl(), 'Visit Website')],
      footerNote: 'PT Havor Smarta Digital - Your Digital IT Partner Solution',
    }),
  });

  const adminEmail = await sendMailSafely({
    to: process.env.ADMIN_EMAIL,
    subject: `New Website Message - ${message.subject}`,
    text: [
      'Ada pesan baru yang dikirim melalui website PT Havor Smarta Digital.',
      '',
      `Name: ${message.name}`,
      `Email: ${message.email}`,
      `Subject: ${message.subject}`,
      '',
      'Message:',
      message.message,
    ].join('\n'),
    html: renderEmailCard({
      title: 'New Website Message',
      preview: `${message.name} sent a new website message.`,
      greeting: 'Hi Admin PT Havor Smarta Digital,',
      body: adminBody,
      detailsTitle: 'Message Details',
      details: [
        { label: 'Name', value: message.name },
        { label: 'Email', value: message.email },
        { label: 'Subject', value: message.subject },
        { label: 'Message', value: message.message },
      ],
      buttons: [renderButton(getFrontendUrl(), 'Visit Website')],
      footerNote: 'Notifikasi ini dikirim dari website PT Havor Smarta Digital.',
    }),
  });

  return { userEmail, adminEmail };
};

module.exports = {
  getMissingEmailConfig,
  isEmailConfigured,
  sendCareerEmails,
  sendContactEmails,
  sendMail,
  sendMailSafely,
};
